<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCapture extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['raw_payload'];

    protected function casts(): array
    {
        return ['raw_payload' => 'encrypted:array', 'occurred_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
