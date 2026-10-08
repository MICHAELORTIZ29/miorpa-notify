<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReconciliation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['result' => 'encrypted:array'];
    }
}
