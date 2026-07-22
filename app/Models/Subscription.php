<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;
    use HasPublicId;

    public const CYCLE_MONTHLY = 'monthly';
    public const CYCLE_ANNUAL = 'annual';

    public const STATUS_TRIAL = 'trial';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_CANCELLED = 'cancelled';

    public const LIMIT_EMITTERS = 'devices.emitters';
    public const LIMIT_RECEIVERS = 'devices.receivers';
    public const LIMIT_CASHIERS = 'users.cashiers';

    protected $fillable = [
        'business_id',
        'plan_id',
        'billing_cycle',
        'status',
        'price',
        'currency',
        'starts_at',
        'current_period_ends_at',
        'grace_ends_at',
        'suspended_at',
        'ended_at',
        'auto_suspend',
        'terms_snapshot_json',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'grace_ends_at' => 'datetime',
            'suspended_at' => 'datetime',
            'ended_at' => 'datetime',
            'auto_suspend' => 'boolean',
            'terms_snapshot_json' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function limitOverrides(): HasMany
    {
        return $this->hasMany(
            SubscriptionLimitOverride::class
        );
    }

    public function limit(string $code): ?int
    {
        $this->loadMissing([
            'limitOverrides',
            'plan.limits',
        ]);

        $override = $this->limitOverrides
            ->firstWhere('limit_code', $code);

        if ($override !== null) {
            return (int) $override->numeric_value;
        }

        $planLimit = $this->plan->limits
            ->firstWhere('limit_code', $code);

        if ($planLimit?->numeric_value === null) {
            return null;
        }

        return (int) $planLimit->numeric_value;
    }

    public function warning(): ?array
    {
        if ($this->status === self::STATUS_SUSPENDED) {
            return [
                'level' => 'danger',
                'code' => 'SUBSCRIPTION_SUSPENDED',
                'message' =>
                    'La suscripción está suspendida. Comunícate con MIORPA para reactivarla.',
            ];
        }

        if ($this->status === self::STATUS_OVERDUE) {
            return [
                'level' => 'warning',
                'code' => 'SUBSCRIPTION_OVERDUE',
                'message' =>
                    'El pago está vencido y se encuentra en periodo de gracia.',
            ];
        }

        $days = (int) now()
            ->startOfDay()
            ->diffInDays(
                $this->current_period_ends_at
                    ->copy()
                    ->startOfDay(),
                false
            );

        if ($days >= 0 && $days <= 7) {
            return [
                'level' => 'info',
                'code' => 'SUBSCRIPTION_DUE_SOON',
                'message' => $days === 0
                    ? 'Tu suscripción vence hoy.'
                    : "Tu suscripción vence en {$days} días.",
            ];
        }

        return null;
    }
}