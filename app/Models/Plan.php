<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'version',
    ];

    public function limits(): HasMany
    {
        return $this->hasMany(PlanLimit::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}