<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\DeviceIncident;
use App\Models\NotificationCapture;
use App\Models\PaymentReconciliation;
use App\Services\DeviceIncidentService;
use App\Services\PaymentReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReliabilityController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;
        $filter = $request->validate(['filter' => ['nullable', Rule::in(['pending', 'all', 'parsed', 'grouped', 'ignored'])]])['filter'] ?? 'pending';
        $q = NotificationCapture::where('business_id', $businessId)->with(['device', 'payment']);
        if ($filter === 'pending') {
            $q->whereIn('state', ['unrecognized', 'parse_error'])->whereNull('reviewed_at');
        } elseif ($filter === 'ignored') {
            $q->whereNotNull('reviewed_at');
        } elseif ($filter !== 'all') {
            $q->where('state', $filter);
        }
        $captures = $q->latest('occurred_at')->paginate(20)->withQueryString();
        $incidents = DeviceIncident::where('business_id', $businessId)->with('device')->latest('opened_at')->limit(50)->get();
        $reconciliations = PaymentReconciliation::where('business_id', $businessId)->latest()->limit(10)->get();

        return view('business.reliability.index', compact('captures', 'incidents', 'reconciliations', 'filter'));
    }

    public function summary(Request $request)
    {
        $q = DeviceIncident::where('business_id', $request->user()->business_id)->whereNull('resolved_at');

        return response()->json(['count' => (clone $q)->count(), 'messages' => $q->with('device')->orderBy('opened_at')->limit(5)->get()->map(fn ($r) => ['device' => $r->device?->name, 'message' => $r->message])]);
    }

    public function review(Request $request, NotificationCapture $capture, DeviceIncidentService $service)
    {
        abort_unless($capture->business_id === $request->user()->business_id, 404);
        $action = $request->validate(['action' => ['required', Rule::in(['ignore', 'retry'])]])['action'];
        DB::transaction(function () use ($capture, $request, $action) {
            $row = NotificationCapture::whereKey($capture->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($row->state, ['unrecognized', 'parse_error']), 422, 'Solo se revisan notificaciones sin pago identificado.');
            if ($action === 'retry') {
                // Do not enqueue repeated commands while one is awaiting the device.
                if ($row->retry_version === $row->applied_retry_version) {
                    $row->increment('retry_version');
                }
                $row->update(['reviewed_at' => null, 'reviewed_by' => null]);
            } else {
                $row->update(['reviewed_at' => now(), 'reviewed_by' => $request->user()->id, 'applied_retry_version' => $row->retry_version]);
            }
        }, 3);
        $service->refresh($capture->device);

        return back()->with('success', $action === 'retry' ? 'Reprocesamiento solicitado. El teléfono lo ejecutará al conectarse; un formato todavía desconocido seguirá en revisión.' : 'Marcada como revisada sin registrar un pago. El original se conserva.');
    }

    public function reconcile(Request $request, PaymentReconciliationService $service)
    {
        $data = $request->validate(['provider' => ['required', Rule::in(['yape', 'plin'])], 'csv' => ['required', 'file', 'max:1024']]);
        $result = $service->compare($request->user()->business_id, $data['provider'], $request->file('csv')->getRealPath(), $request->user()->business->timezone ?: 'America/Lima');
        $row = PaymentReconciliation::create(['business_id' => $request->user()->business_id, 'user_id' => $request->user()->id, 'provider_code' => $data['provider'], 'result' => $result, 'total' => count($result['rows'])]);

        return redirect()->route('business.reliability.reconciliation', $row);
    }

    public function reconciliation(Request $request, PaymentReconciliation $reconciliation)
    {
        abort_unless($reconciliation->business_id === $request->user()->business_id, 404);

        return view('business.reliability.reconciliation',compact('reconciliation'));
    }
}
