<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class StoreBusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'admin_email' => Str::lower(
                trim((string) $this->admin_email)
            ),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'legal_name' => ['nullable', 'string', 'max:200'],
            'tax_id' => ['nullable', 'string', 'max:30'],
            'contact_name' => ['nullable', 'string', 'max:150'],
            'contact_email' => ['nullable', 'email', 'max:254'],
            'contact_phone' => ['nullable', 'string', 'max:30'],

            'admin_name' => ['required', 'string', 'max:150'],
            'admin_email' => [
                'required',
                'email',
                'max:254',
                'unique:users,email',
            ],
            'admin_phone' => ['nullable', 'string', 'max:30'],
            'admin_password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre comercial',
            'legal_name' => 'razón social',
            'tax_id' => 'RUC',
            'admin_name' => 'nombre del administrador',
            'admin_email' => 'correo del administrador',
            'admin_phone' => 'teléfono del administrador',
            'admin_password' => 'contraseña',
        ];
    }
}