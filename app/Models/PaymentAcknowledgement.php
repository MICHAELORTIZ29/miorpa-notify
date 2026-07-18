<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAcknowledgement extends Model
{
    use HasFactory;
    use HasPublicId;

    protected $fillable = [
        'payment_id',
        'user_id',
        'receiver_device_id',
        'viewed_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receiverDevice(): BelongsTo
    {
        return $this->belongsTo(
            Device::class,
            'receiver_device_id'
        );
    }
}