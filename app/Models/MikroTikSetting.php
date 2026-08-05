<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['ip', 'username', 'password', 'port'])]
class MikroTikSetting extends Model
{
    protected $table = 'mikrotik_settings';

    protected $casts = [
        'port' => 'integer',
    ];
}
