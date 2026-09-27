<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class PushNotificationScreen extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Push Notifications';

    protected static string $view = 'filament.pages.push-notification-screen';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id') 
                    ->label('Target User')
                    ->options(function () {
                        $options = ['all' => '📢 Send to All Users (with active FCM tokens)'];
                        $users = User::orderBy('name')->get();
                        foreach ($users as $u) {
                            $status = !empty($u->fcm_token) ? '✅ [Active Token]' : '⚠️ [No FCM Token]';
                            $options[$u->id] = "{$u->name} ({$u->email}) - {$status}";
                        }
                        return $options;
                    })
                    ->required()
                    ->searchable()
                    ->helperText('Select a user. Users marked with ⚠️ [No FCM Token] must first log into the Flutter mobile app on an Android phone with notifications allowed.'),
                TextInput::make('title')
                    ->label('Notification Title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->label('Notification Body')
                    ->required()
                    ->rows(3)
                    ->maxLength(500),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Notification')
                ->submit('sendNotification')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane'),
        ];
    }

    public function sendNotification(): void
    {
        $data = $this->form->getState();

        try {
            // Bypass Facade and inject Guzzle options directly to fix local Windows SSL issues
            $options = \Kreait\Firebase\Http\HttpClientOptions::default()
                ->withGuzzleConfigOptions(['verify' => storage_path('app/cacert.pem')]);

            $factory = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(base_path(config('firebase.projects.app.credentials')))
                ->withHttpClientOptions($options);

            $messaging = $factory->createMessaging();
            $notification = Notification::create($data['title'], $data['body']);

            $successCount = 0;

            if ($data['user_id'] === 'all') {
                $tokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
                
                if (empty($tokens)) {
                    FilamentNotification::make()
                        ->title('No valid FCM tokens found in the database.')
                        ->warning()
                        ->send();
                    return;
                }

                $message = CloudMessage::new()->withNotification($notification);
                $report = $messaging->sendMulticast($message, $tokens);
                $successCount = $report->successes()->count();

            } else {
                $user = User::find($data['user_id']);
                if ($user && $user->fcm_token) {
                    $message = CloudMessage::withTarget('token', $user->fcm_token)
                        ->withNotification($notification);
                    $messaging->send($message);
                    $successCount = 1;
                } else {
                    FilamentNotification::make()
                        ->title('Selected user does not have a valid FCM token.')
                        ->danger()
                        ->send();
                    return;
                }
            }

            FilamentNotification::make()
                ->title("Notification sent successfully to {$successCount} device(s)!")
                ->success()
                ->send();

            $this->form->fill();

        } catch (\Exception $e) {
            FilamentNotification::make()
                ->title('Failed to send notification')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
