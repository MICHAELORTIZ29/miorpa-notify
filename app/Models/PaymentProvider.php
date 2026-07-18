<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentProvider extends Model
{
    use HasFactory;
    use HasPublicId;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'code',
        'name',
        'android_packages',
        'status',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'android_packages' => 'array',
            'configuration' => 'array',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}