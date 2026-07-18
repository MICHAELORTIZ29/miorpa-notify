<?php

namespace App\Http\Requests\Business;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() === true
            && $this->user()?->business_id !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'email' => strtolower(trim((string) $this->email)),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],

            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'role_code' => [
                'required',
                Rule::in([User::ROLE_CASHIER]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ingresa el nombre del cajero.',
            'email.required' => 'Ingresa el correo del cajero.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
            'password.required' => 'Ingresa una contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role_code.in' => 'El tipo de usuario seleccionado no es válido.',
        ];
    }
}