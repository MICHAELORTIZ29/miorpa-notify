<?php

namespace App\Http\Requests\Business;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role_code, [
            User::ROLE_ADMINISTRATOR,
            User::ROLE_CASHIER,
        ], true);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:190'],
            'provider' => ['nullable', 'string', 'max:40'],
            'status' => [
                'nullable',
                Rule::in([
                    Payment::STATUS_RECEIVED,
                    Payment::STATUS_CONFIRMED,
                    Payment::STATUS_IGNORED,
                ]),
            ],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
            'amount' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'gt:0',
            ],
        ];
    }
}