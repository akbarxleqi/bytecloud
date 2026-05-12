<?php

use App\Models\TelegramAccount;
use App\Services\Telegram\TelegramAuthService;
use danog\MadelineProto\LocalFile;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$account = TelegramAccount::first();
$service = new TelegramAuthService();
$api = $service->api($account);

$testFile = __DIR__.'/../public/favicon.ico';

try {
    echo "Calling sendDocument with LocalFile...\n";
    $result = $api->sendDocument('me', new LocalFile($testFile), 'TEST LocalFile debug');

    echo "\n=== FULL RESULT ===\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
