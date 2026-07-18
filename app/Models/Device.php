<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;
    use HasPublicId;

    public const TYPE_EMITTER = 'emitter';
    public const TYPE_RECEIVER = 'receiver';

    public const PLATFORM_ANDROID = 'android';
    public const PLATFORM_WEB = 'web';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';
    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'business_id',
        'authorized_by',
        'name',
        'type',
        'platform',
        'status',
        'token_hash',
        'installation_hash',
        'app_version',
        'last_ip',
        'user_agent',
        'capabilities',
        'authorized_at',
        'last_seen_at',
        'disabled_at',
        'revoked_at',
    ];

    protected $hidden = [
        'token_hash',
        'installation_hash',
    ];

    protected function casts(): array
    {
        return [
            'capabilities' => 'array',
            'authorized_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'disabled_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function pairingCodes(): HasMany
    {
        return $this->hasMany(PairingCode::class, 'used_by_device_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->revoked_at === null;
    }
    public function emittedPayments(): HasMany
{
    return $this->hasMany(Payment::class, 'emitter_device_id');
}
}