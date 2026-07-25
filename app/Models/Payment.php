<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'transaction_id', 'order_id', 'phone', 'name', 'package', 'profile',
    'amount', 'status', 'wifi_token', 'wifi_password', 'client_mac',
    'client_ip', 'payment_method', 'payment_response', 'paid_at',
])]
class Payment extends Model
{
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(HotspotSession::class, 'client_mac', 'client_mac');
    }
}
