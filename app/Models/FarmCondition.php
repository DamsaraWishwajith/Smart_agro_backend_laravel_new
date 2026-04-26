<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmCondition extends Model
{
    protected $fillable = [
        'device_id',
        'temp',
        'humidity',
    ];
}
