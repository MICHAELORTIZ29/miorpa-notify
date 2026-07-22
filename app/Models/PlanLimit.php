<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanLimit extends Model
{
    protected $fillable = [
        'plan_id',
        'limit_code',
        'numeric_value',
        'boolean_value',
        'text_value',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:4',
            'boolean_value' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}