<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    protected $fillable = [
        'user_id',
        'crop_name',
        'planted_days',
        'temperature',
        'plants_count',
        'water_time_seconds',
        'water_need_ml',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
