<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    putenv("SSL_CERT_FILE=" . storage_path('app/cacert.pem'));
    $messaging = Kreait\Laravel\Firebase\Facades\Firebase::messaging();
    $message = Kreait\Firebase\Messaging\CloudMessage::withTarget('token', 'fake_token')
        ->withNotification(Kreait\Firebase\Messaging\Notification::create('Test', 'Test'));
    $messaging->send($message);
    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error Class: " . get_class($e) . "\n";
    echo "Error Message: " . $e->getMessage() . "\n";
}
