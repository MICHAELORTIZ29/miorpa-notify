<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class DeviceHeartbeatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_version' => ['nullable', 'string', 'max:40'],
            'capabilities' => ['nullable', 'array'],
            'diagnostics' => ['nullable', 'array:listener_connected,notification_permission,last_connected_at,last_notification_at,last_detected_at,last_unrecognized_at,capture_error,pending_payments,blocked_payments,other_session_payments,last_upload_error,unrecognized_notifications,oldest_pending_at'],
            'diagnostics.unrecognized_notifications' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'diagnostics.oldest_pending_at' => ['nullable', 'integer', 'min:0'],
            'diagnostics.listener_connected' => ['nullable', 'boolean'],
            'diagnostics.notification_permission' => ['nullable', 'boolean'],
            'diagnostics.last_connected_at' => ['nullable', 'integer', 'min:0'],
            'diagnostics.last_notification_at' => ['nullable', 'integer', 'min:0'],
            'diagnostics.last_detected_at' => ['nullable', 'integer', 'min:0'],
            'diagnostics.last_unrecognized_at' => ['nullable', 'integer', 'min:0'],
            'diagnostics.capture_error' => ['nullable', 'string', 'max:500'],
            'diagnostics.last_upload_error' => ['nullable', 'string', 'max:500'],
            'diagnostics.pending_payments' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'diagnostics.blocked_payments' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'diagnostics.other_session_payments' => ['nullable', 'integer', 'min:0', 'max:10000000'],
        ];
    }
}
