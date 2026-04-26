<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'drip',
        'mist',
        'exhaust',
        'light',
    ];
}
