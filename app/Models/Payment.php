<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;
    use HasPublicId;

    public const STATUS_RECEIVED = 'received';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IGNORED = 'ignored';

    protected $fillable = [
        'business_id',
        'payment_provider_id',
        'emitter_device_id',
        'source_event_hash',
        'external_reference',
        'payer_name',
        'payer_document',
        'amount',
        'currency',
        'status',
        'parser_version',
        'occurred_at',
        'received_at',
        'raw_payload',
        'metadata',
    ];

    protected $hidden = [
        'raw_payload',
        'source_event_hash',
        'payer_document',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
            'received_at' => 'datetime',
            'raw_payload' => 'encrypted:array',
            'metadata' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(
            PaymentProvider::class,
            'payment_provider_id'
        );
    }

    public function emitterDevice(): BelongsTo
    {
        return $this->belongsTo(
            Device::class,
            'emitter_device_id'
        );
    }

    public function acknowledgements(): HasMany
    {
        return $this->hasMany(PaymentAcknowledgement::class);
    }
}