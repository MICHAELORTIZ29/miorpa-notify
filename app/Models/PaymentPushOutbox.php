<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPushOutbox extends Model
{
    protected $table = 'payment_push_outbox';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['delivered_subscription_ids' => 'array', 'next_attempt_at' => 'datetime',
            'lease_until' => 'datetime', 'delivered_at' => 'datetime'];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
