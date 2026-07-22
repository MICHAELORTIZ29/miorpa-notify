<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;
    use HasPublicId;

    public const STATUS_TRIAL = 'trial';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_CLOSED = 'closed';
    public const SUSPENSION_MANUAL = 'manual';
public const SUSPENSION_NONPAYMENT = 'nonpayment';

    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'timezone',
        'status',
        'contact_name',
        'contact_email',
        'contact_phone',
        'suspended_at',
        'closed_at',
        'suspension_reason',
    ];

    protected function casts(): array
    {
        return [
            'suspended_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this
            ->hasOne(Subscription::class)
            ->latestOfMany();
    }

    public function isActive(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_TRIAL,
                self::STATUS_ACTIVE,
                self::STATUS_OVERDUE,
            ],
            true
        );
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
    
}