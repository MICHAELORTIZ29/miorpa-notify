<?php

namespace App\Http\Requests\Receiver;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ReceiverLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->business_id !== null
            && in_array(
                $user->role_code,
                [
                    User::ROLE_ADMINISTRATOR,
                    User::ROLE_CASHIER,
                ],
                true
            );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(
                trim((string) $this->code)
            ),

            'device_name' => trim(
                (string) $this->device_name
            ),

            'installation_id' => trim(
                (string) $this->installation_id
            ),
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

            'device_name' => [
                'required',
                'string',
                'max:120',
            ],

            'installation_id' => [
                'required',
                'string',
                'min:16',
                'max:190',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' =>
                'Ingresa el código de vinculación.',

            'code.regex' =>
                'El código no tiene el formato correcto.',

            'device_name.required' =>
                'Escribe un nombre para este dispositivo.',

            'installation_id.required' =>
                'No se pudo identificar este navegador.',

            'installation_id.min' =>
                'El identificador del navegador no es válido.',
        ];
    }
}