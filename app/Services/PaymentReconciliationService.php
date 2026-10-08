<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class PaymentReconciliationService
{
    public function compare(int $businessId, string $provider, string $path, string $timezone): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw ValidationException::withMessages(['csv' => 'No se pudo leer el archivo.']);
        }
        try {
            $header = fgetcsv($handle, 0, ',', '"', '');
            if (! $header) {
                throw ValidationException::withMessages(['csv' => 'El CSV está vacío.']);
            }
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            if ($header !== ['fecha', 'monto', 'referencia', 'nombre']) {
                throw ValidationException::withMessages(['csv' => 'Usa las columnas fecha,monto,referencia,nombre, separadas por comas.']);
            }
            $rows = [];
            $refs = [];
            while (($values = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                if ($values === [null]) {
                    continue;
                }
                $line = count($rows) + 2;
                if (count($rows) >= 500 || count($values) !== 4) {
                    throw ValidationException::withMessages(['csv' => "Fila $line inválida; máximo 500 movimientos por archivo."]);
                }
                [$date,$amount,$ref,$name] = array_map('trim', $values);
                if (! mb_check_encoding(implode('', $values), 'UTF-8') || strlen($ref) > 100 || mb_strlen($name) > 150 || ! preg_match('/^[0-9]{1,8}(?:\.[0-9]{1,2})?$/D', $amount) || (float) $amount <= 0) {
                    throw ValidationException::withMessages(['csv' => "Fila $line: usa UTF-8 y monto positivo con punto decimal, sin separadores de miles."]);
                }
                if (! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D', $date)) {
                    throw ValidationException::withMessages(['csv' => "Fila $line: fecha requerida como AAAA-MM-DD HH:MM:SS."]);
                }
                try {
                    $at = CarbonImmutable::createFromFormat('!Y-m-d H:i:s', $date, $timezone);
                } catch (\Throwable) {
                    $at = null;
                }
                if (! $at || $at->format('Y-m-d H:i:s') !== $date || $at->greaterThan(now()->addMinutes(5))) {
                    throw ValidationException::withMessages(['csv' => "Fila $line: fecha inválida o futura."]);
                }
                $amount = number_format((float) $amount, 2, '.', '');
                $q = Payment::where('business_id', $businessId)->whereHas('provider', fn ($q) => $q->where('code', $provider))->where('currency', 'PEN')->where('amount', $amount);
                $exact = $ref === '' ? collect() : (clone $q)->where('external_reference', $ref)->limit(6)->get();
                $matches = $exact->isNotEmpty() ? $exact : $q->whereBetween('occurred_at', [$at->utc()->subMinutes(5), $at->utc()->addMinutes(5)])->orderBy('occurred_at')->limit(6)->get();
                $rows[] = ['line' => $line, 'date' => $date, 'amount' => $amount, 'reference' => $ref, 'name' => $name,
                    'status' => $matches->isEmpty() ? 'missing' : ($matches->count() === 1 ? 'candidate' : 'ambiguous'),
                    'match_method' => $exact->isNotEmpty() ? 'reference' : 'amount_time',
                    'candidates' => $matches->map(fn ($p) => ['id' => $p->public_id, 'name' => $p->payer_name, 'date' => $p->occurred_at->timezone($timezone)->format('Y-m-d H:i:s')])->all()];
                if ($ref !== '') {
                    $refs[$ref] = ($refs[$ref] ?? 0) + 1;
                }
            }
            if (! $rows) {
                throw ValidationException::withMessages(['csv' => 'El CSV no contiene movimientos.']);
            }
            $uses = [];
            foreach ($rows as $row) {
                foreach ($row['candidates'] as $candidate) {
                    $uses[$candidate['id']] = ($uses[$candidate['id']] ?? 0) + 1;
                }
            }
            foreach ($rows as &$row) {
                if ($row['reference'] !== '' && $refs[$row['reference']] > 1) {
                    $row['status'] = 'duplicate_reference';
                } elseif (count($row['candidates']) === 1 && $uses[$row['candidates'][0]['id']] > 1) {
                    $row['status'] = 'ambiguous';
                }
            }
            unset($row);

            return ['rows' => $rows, 'timezone' => $timezone, 'summary' => array_count_values(array_column($rows,'status'))];
        } finally {
            fclose($handle);
        }
    }
}
