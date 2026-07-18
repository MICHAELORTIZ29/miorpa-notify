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
            'diagnostics' => ['nullable', 'array'],
        ];
    }
}