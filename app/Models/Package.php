<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'name', 'price', 'duration', 'speed', 'profile', 'session_timeout', 'icon',
])]
class Package extends Model
{
    protected $table = 'packages';

    protected $casts = [
        'price' => 'integer',
        'session_timeout' => 'integer',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(HotspotSession::class, 'package', 'profile');
    }
}
