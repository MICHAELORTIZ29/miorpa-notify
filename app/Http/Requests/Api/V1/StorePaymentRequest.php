<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'provider_code' => strtolower(
                trim((string) $this->provider_code)
            ),
            'event_id' => trim((string) $this->event_id),
            'payer_name' => trim((string) $this->payer_name),
            'currency' => strtoupper(
                trim((string) ($this->currency ?: 'PEN'))
            ),
        ]);
    }

    public function rules(): array
    {
        return [
            'provider_code' => [
                'required',
                'string',
                'max:40',
            ],
            'event_id' => [
                'required',
                'string',
                'min:8',
                'max:190',
            ],
            'external_reference' => [
                'nullable',
                'string',
                'max:120',
            ],
            'payer_name' => [
                'nullable',
                'string',
                'max:190',
            ],
            'amount' => [
                'required',
                'numeric',
                'decimal:0,2',
                'gt:0',
                'max:999999999999.99',
            ],
            'currency' => [
                'required',
                Rule::in(['PEN']),
            ],
            'occurred_at' => [
                'required',
                'date',
            ],
            'parser_version' => [
                'nullable',
                'string',
                'max:40',
            ],
            'raw_payload' => [
                'nullable',
                'array',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'provider_code.required' => 'Indica el medio de pago.',
            'event_id.required' => 'Falta el identificador del evento.',
            'event_id.min' => 'El identificador del evento no es válido.',
            'amount.required' => 'Indica el monto recibido.',
            'amount.numeric' => 'El monto debe ser numérico.',
            'amount.decimal' => 'El monto admite como máximo dos decimales.',
            'amount.gt' => 'El monto debe ser mayor que cero.',
            'currency.in' => 'La moneda todavía no es compatible.',
            'occurred_at.required' => 'Indica la fecha del pago.',
            'occurred_at.date' => 'La fecha del pago no es válida.',
        ];
    }
}