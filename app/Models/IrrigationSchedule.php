<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrrigationSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'system_type',
        'days',
        'on_time',
        'off_time',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
