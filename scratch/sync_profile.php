<?php

use App\Models\TelegramAccount;
use App\Services\Telegram\TelegramAuthService;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$account = TelegramAccount::first();
$service = new TelegramAuthService();

try {
    echo "Syncing profile for: " . $account->phone_number . "\n";
    $service->syncProfile($account);
    echo "Done! Name: " . $account->fresh()->name . "\n";
    echo "Photo: " . ($account->fresh()->meta['photo_url'] ?? 'N/A') . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
