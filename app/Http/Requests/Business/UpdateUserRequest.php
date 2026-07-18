<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authenticatedUser = $this->user();
        $cashier = $this->route('user');

        return $authenticatedUser?->isAdministrator() === true
            && $authenticatedUser->business_id !== null
            && $cashier !== null
            && $cashier->business_id === $authenticatedUser->business_id
            && $cashier->isCashier();
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
        $cashier = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:120'],

            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')->ignore($cashier?->id),
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
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
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}