<?php

namespace App\Services\Drive;

use App\Models\DriveFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriveDownloadService
{
    public function stream(DriveFile $file, string $downloadType = 'private'): StreamedResponse
    {
        $file->downloadLogs()->create([
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'download_type' => $downloadType,
        ]);

        if ($file->tmp_path && Storage::disk(config('drive.tmp_disk'))->exists($file->tmp_path)) {
            return Storage::disk(config('drive.tmp_disk'))->download($file->tmp_path, $file->original_name);
        }

        $account = $file->telegramAccount;
        $authService = new \App\Services\Telegram\TelegramAuthService;
        $api = $authService->api($account);

        return response()->streamDownload(function () use ($api, $file): void {
            $api->downloadToCallable([
                'peer' => 'me',
                'id' => (int) $file->telegram_message_id,
            ], function (string $chunk): void {
                echo $chunk;
                flush();
            });
        }, $file->original_name, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'Content-Length' => $file->size_bytes,
        ]);
    }
}
