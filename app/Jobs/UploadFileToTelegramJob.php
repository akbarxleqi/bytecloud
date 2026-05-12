<?php

namespace App\Jobs;

use App\Models\DriveFile;
use App\Services\Telegram\TelegramUploadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class UploadFileToTelegramJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $driveFileId) {}

    /**
     * Execute the job.
     */
    public function handle(TelegramUploadService $telegramUploadService): void
    {
        $file = DriveFile::findOrFail($this->driveFileId);

        try {
            $file->update(['status' => 'uploading', 'error_message' => null]);

            if (! $file->tmp_path) {
                throw new RuntimeException('No temporary file path is attached to this upload.');
            }

            $absolutePath = Storage::disk(config('drive.tmp_disk'))->path($file->tmp_path);
            $result = $telegramUploadService->upload($file, $absolutePath);

            $file->update([
                'telegram_chat_id' => $result->chatId,
                'telegram_message_id' => $result->messageId,
                'telegram_file_id' => $result->fileId,
                'telegram_meta' => $result->meta,
                'status' => 'uploaded',
                'uploaded_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $file->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            report($exception);
        }
    }
}
