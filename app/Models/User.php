<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'device_id',
        'status',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the plants associated with the user.
     */
    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    /**
     * Get the motors associated with the user.
     */
    public function motors()
    {
        return $this->hasMany(Motor::class);
    }

    /**
     * Get the irrigation schedules associated with the user.
     */
    public function irrigationSchedules()
    {
        return $this->hasMany(IrrigationSchedule::class);
    }

    /**
     * Get the ESP notifications associated with the user.
     */
    public function espNotifications()
    {
        return $this->hasMany(EspNotification::class);
    }

    /**
     * Get the messages associated with the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Send real-time FCM Push Notification helper.
     */
    public function sendPushNotification(string $title, string $body)
    {
        if (!$this->fcm_token) {
            return;
        }

        try {
            $unreadCount = Message::where('user_id', $this->id)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->count();

            // Suffix title with the unread count if it's greater than 0
            $displayTitle = $unreadCount > 0 
                ? "$title ($unreadCount)" 
                : $title;

            $options = \Kreait\Firebase\Http\HttpClientOptions::default()
                ->withGuzzleConfigOptions(['verify' => storage_path('app/cacert.pem')]);

            $factory = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(base_path(config('firebase.projects.app.credentials')))
                ->withHttpClientOptions($options);

            $messaging = $factory->createMessaging();
            $notification = \Kreait\Firebase\Messaging\Notification::create($displayTitle, $body);

            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $this->fcm_token)
                ->withNotification($notification)
                ->withData([
                    'unread_count' => (string)$unreadCount,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]);

            $messaging->send($message);
        } catch (\Exception $e) {
            \Log::error('FCM Push Notification failed: ' . $e->getMessage());
        }
    }
}
