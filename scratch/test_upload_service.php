<?php

use App\Models\TelegramAccount;
use App\Models\DriveFile;
use App\Services\Telegram\TelegramAuthService;
use App\Services\Telegram\TelegramUploadService;
use Illuminate\Support\Facades\Storage;
use danog\MadelineProto\LocalFile;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get first failed file
$file = DriveFile::where('status', 'failed')->first();
if (!$file) {
    echo "No failed files found.\n";
    exit;
}

echo "Testing upload for: {$file->original_name}\n";
echo "tmp_path: {$file->tmp_path}\n";

$localPath = Storage::disk(config('drive.tmp_disk'))->path($file->tmp_path);
echo "Local path: {$localPath}\n";
echo "File exists: " . (file_exists($localPath) ? 'YES' : 'NO') . "\n\n";

if (!file_exists($localPath)) {
    echo "ERROR: temp file missing!\n";
    exit;
}

$service = new TelegramUploadService();
try {
    $result = $service->upload($file, $localPath);
    echo "SUCCESS!\n";
    echo "Message ID: {$result->messageId}\n";
    echo "File ID: {$result->fileId}\n";
    print_r($result->meta);
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
