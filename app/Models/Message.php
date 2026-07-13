<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'sender_type',
        'message',
        'attachment_path',
        'attachment_type',
        'is_read',
    ];

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Send push notification automatically when admin sends a message.
     */
    protected static function booted()
    {
        static::created(function ($message) {
            if ($message->sender_type === 'admin') {
                $user = $message->user;
                if ($user) {
                    $body = $message->message ?: 'Sent an attachment';
                    $user->sendPushNotification('New Message from SmartAgro Support', $body);
                }
            }
        });
    }
}
