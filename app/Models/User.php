<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use HasPublicId;
    use Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMINISTRATOR = 'administrator';
    public const ROLE_CASHIER = 'cashier';

    public const STATUS_INVITED = 'invited';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'business_id',
        'role_code',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'email_verified_at',
        'last_login_at',
        'password_changed_at',
        'disabled_at',
        'created_by_user_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'disabled_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by_user_id');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(self::class, 'created_by_user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_code === self::ROLE_SUPERADMIN;
    }

    public function isAdministrator(): bool
    {
        return $this->role_code === self::ROLE_ADMINISTRATOR;
    }

    public function isCashier(): bool
    {
        return $this->role_code === self::ROLE_CASHIER;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
   
}