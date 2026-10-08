<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\NotificationCapture;
use App\Models\Payment;
use App\Services\DeviceIncidentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NotificationCaptureController extends Controller
{
    public function sync(Request $request, DeviceIncidentService $incidents)
    {
        $device = $request->attributes->get('device');
        abort_unless($device->type === Device::TYPE_EMITTER && $device->platform === Device::PLATFORM_ANDROID, 403);
        $data = $request->validate([
            'captures' => ['present', 'array', 'max:20'],
            'captures.*' => ['array:id,package_name,state,reason,occurred_at,raw_payload,event_id,provider_code,revision,applied_retry_version'],
            'captures.*.id' => ['required', 'regex:/^[a-f0-9]{64}$/', 'distinct'],
            'captures.*.package_name' => ['required', Rule::in(['com.bcp.innovacxion.yapeapp', 'pe.com.interbank.mobilebanking', 'com.bbva.nxt_peru'])],
            'captures.*.state' => ['required', Rule::in(['parsed', 'unrecognized', 'parse_error', 'grouped', 'captured'])],
            'captures.*.reason' => ['nullable', 'string', 'max:80'], 'captures.*.occurred_at' => ['required', 'date', 'before_or_equal:'.now()->addMinutes(5)->toIso8601String()],
            'captures.*.event_id' => ['nullable', 'string', 'max:100'], 'captures.*.provider_code' => ['nullable', Rule::in(['yape', 'plin'])],
            'captures.*.revision' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'captures.*.applied_retry_version' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'captures.*.raw_payload' => ['required', 'array:title,text,big_text,text_lines,sub_text,summary_text,info_text,ticker_text,group_summary'],
            'captures.*.raw_payload.title' => ['nullable', 'string', 'max:1000'],
            'captures.*.raw_payload.text' => ['nullable', 'string', 'max:8000'],
            'captures.*.raw_payload.big_text' => ['nullable', 'string', 'max:8000'],
            'captures.*.raw_payload.sub_text' => ['nullable', 'string', 'max:1000'],
            'captures.*.raw_payload.summary_text' => ['nullable', 'string', 'max:1000'],
            'captures.*.raw_payload.info_text' => ['nullable', 'string', 'max:1000'],
            'captures.*.raw_payload.ticker_text' => ['nullable', 'string', 'max:1000'],
            'captures.*.raw_payload.group_summary' => ['required', 'boolean'],
            'captures.*.raw_payload.text_lines' => ['present', 'array', 'max:20'],
            'captures.*.raw_payload.text_lines.*' => ['string', 'max:1000'],
        ]);
        $ack = DB::transaction(function () use ($device, $data) {
            Device::whereKey($device->id)->lockForUpdate()->firstOrFail();
            $ack = [];
            foreach ($data['captures'] as $item) {
                $row = NotificationCapture::firstOrNew(['device_id' => $device->id, 'capture_id' => $item['id']]);
                if (! $row->exists || $item['revision'] > $row->revision) {
                    $row->fill(['business_id' => $device->business_id, 'package_name' => $item['package_name'], 'state' => $item['state'],
                        'reason' => $item['reason'] ?? null, 'occurred_at' => CarbonImmutable::parse($item['occurred_at'])->utc(),
                        'raw_payload' => $item['raw_payload'], 'event_id' => $item['event_id'] ?? null, 'provider_code' => $item['provider_code'] ?? null,
                        'revision' => $item['revision'], 'applied_retry_version' => max($row->applied_retry_version ?? 0, min($item['applied_retry_version'], $row->retry_version ?? 0))]);
                    if ($row->event_id && $row->provider_code) {
                        $row->payment_id = Payment::where('business_id', $device->business_id)->where('source_event_hash', hash('sha256', $row->provider_code.'|'.$row->event_id))->value('id');
                    }
                    $row->save();
                }
                $ack[] = ['id' => $item['id'], 'revision' => $item['revision']];
            }

            return $ack;
        }, 3);
        $incidents->refresh($device);
        $retry = NotificationCapture::where('device_id', $device->id)->whereNull('reviewed_at')->whereColumn('retry_version', '>', 'applied_retry_version')->orderBy('id')->limit(50)->get()->map(fn ($r) => ['id' => $r->capture_id, 'version' => $r->retry_version]);

        return response()->json(['data' => ['acknowledged' => $ack, 'retries' => $retry]]);
    }
}
