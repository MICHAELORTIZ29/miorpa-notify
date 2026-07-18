<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RedeemPairingCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->code)),
            'name' => trim((string) $this->name),
            'installation_id' => trim((string) $this->installation_id),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'regex:/^MNP-[A-HJ-NP-Z2-9]{4}-[A-HJ-NP-Z2-9]{4}$/',
            ],
            'name' => ['required', 'string', 'max:120'],
            'installation_id' => ['required', 'string', 'min:16', 'max:190'],
            'platform' => [
                'required',
                Rule::in(['android', 'web']),
            ],
            'app_version' => ['nullable', 'string', 'max:40'],
            'capabilities' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Ingresa el código de vinculación.',
            'code.regex' => 'El formato del código no es válido.',
            'name.required' => 'Ingresa un nombre para el dispositivo.',
            'installation_id.required' => 'Falta el identificador de instalación.',
            'installation_id.min' => 'El identificador de instalación no es válido.',
            'platform.in' => 'La plataforma no es compatible.',
        ];
    }
}