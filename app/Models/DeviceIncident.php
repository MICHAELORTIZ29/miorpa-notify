<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceIncident extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['opened_at' => 'datetime', 'last_observed_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
