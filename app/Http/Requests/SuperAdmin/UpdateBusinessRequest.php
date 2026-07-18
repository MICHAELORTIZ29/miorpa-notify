<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'legal_name' => trim((string) $this->legal_name),
            'tax_id' => preg_replace('/\D/', '', (string) $this->tax_id),
            'contact_phone' => preg_replace('/\D/', '', (string) $this->contact_phone),
            'admin_name' => trim((string) $this->admin_name),
            'admin_email' => strtolower(trim((string) $this->admin_email)),
        ]);
    }

    public function rules(): array
    {
        $business = $this->route('business');

        $administrator = $business?->users()
            ->where('role_code', User::ROLE_ADMINISTRATOR)
            ->oldest('id')
            ->first();

        return [
            'name' => ['required', 'string', 'max:120'],
            'legal_name' => ['nullable', 'string', 'max:160'],

            'tax_id' => [
                'nullable',
                'digits:11',
                Rule::unique('businesses', 'tax_id')->ignore($business?->id),
            ],

            'contact_phone' => ['nullable', 'digits_between:7,15'],
            'timezone' => ['required', Rule::in(['America/Lima'])],

            'admin_name' => ['required', 'string', 'max:120'],

            'admin_email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')->ignore($administrator?->id),
            ],

            'admin_password' => [
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
            'name.required' => 'Ingresa el nombre comercial.',
            'tax_id.digits' => 'El RUC debe contener exactamente 11 números.',
            'tax_id.unique' => 'Este RUC ya está registrado.',
            'contact_phone.digits_between' => 'Ingresa un teléfono válido.',
            'admin_name.required' => 'Ingresa el nombre del administrador.',
            'admin_email.required' => 'Ingresa el correo del administrador.',
            'admin_email.email' => 'Ingresa un correo válido.',
            'admin_email.unique' => 'Este correo ya pertenece a otro usuario.',
            'admin_password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}