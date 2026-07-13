<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    protected $fillable = [
        'user_id',
        'mode',
        'mist_auto_schedule',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
