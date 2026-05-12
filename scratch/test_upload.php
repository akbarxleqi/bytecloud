<?php

use App\Models\TelegramAccount;
use App\Services\Telegram\TelegramAuthService;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$account = TelegramAccount::first();
$service = new TelegramAuthService();
$api = $service->api($account);

try {
    echo "Testing upload...\n";
    $message = $api->messages->sendMedia([
        'peer' => 'me',
        'media' => [
            '_' => 'inputMediaUploadedDocument',
            'file' => __DIR__.'/../public/favicon.ico',
            'attributes' => [
                ['_' => 'documentAttributeFilename', 'file_name' => 'test.ico']
            ]
        ],
        'message' => 'Test upload',
    ]);
    print_r($message);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
