<?php

namespace App\Http\Requests\Business;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePairingCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() === true
            && $this->user()?->business_id !== null;
    }

    public function rules(): array
    {
        return [
            'device_type' => [
                'required',
                Rule::in([
                    Device::TYPE_EMITTER,
                    Device::TYPE_RECEIVER,
                ]),
            ],

            'valid_minutes' => [
                'required',
                'integer',
                Rule::in([5, 10, 15, 30]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'device_type.required' => 'Selecciona el tipo de dispositivo.',
            'device_type.in' => 'El tipo de dispositivo no es válido.',
            'valid_minutes.required' => 'Selecciona la duración del código.',
            'valid_minutes.in' => 'La duración seleccionada no es válida.',
        ];
    }
}