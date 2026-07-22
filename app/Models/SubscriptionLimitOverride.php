<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionLimitOverride extends Model
{
    protected $fillable = [
        'subscription_id',
        'limit_code',
        'numeric_value',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:4',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}