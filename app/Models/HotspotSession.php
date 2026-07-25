<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'session_id', 'mac_address', 'ip_address', 'username', 'package',
    'profile', 'total_seconds', 'used_seconds', 'status',
    'connected_at', 'disconnected_at', 'expires_at',
])]
class HotspotSession extends Model
{
    protected $casts = [
        'total_seconds' => 'integer',
        'used_seconds' => 'integer',
        'connected_at' => 'datetime',
        'disconnected_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'client_mac', 'client_mac');
    }
}
