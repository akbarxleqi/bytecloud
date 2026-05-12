<?php

namespace App\Services\Telegram;

use App\Models\DriveFile;
use danog\MadelineProto\LocalFile;
use RuntimeException;

class TelegramUploadService
{
    public function upload(DriveFile $file, string $localPath): TelegramUploadResult
    {
        if (!file_exists($localPath)) {
            throw new RuntimeException("Temporary file not found: {$localPath}");
        }

        $account = $file->telegramAccount;
        // Create a fresh standalone API instance (not sharing the web IPC session)
        $api = (new TelegramAuthService)->apiFresh($account);

        // Use MadelineProto high-level sendDocument with LocalFile wrapper
        // Signature: sendDocument(peer, file, thumb=null, caption='', ..., fileName=null, mimeType=null, ...)
        $message = $api->sendDocument(
            peer: 'me',
            file: new LocalFile($localPath),
            thumb: null,
            caption: 'Uploaded via Bytecloud',
            callback: function ($completed, $total) use ($file) {
                if ($total > 0) {
                    $progress = min(100, round(($completed / $total) * 100));
                    // Update only if progress changed to avoid too many DB writes
                    if ($progress != $file->upload_progress) {
                        $file->update(['upload_progress' => $progress]);
                    }
                }
            },
            fileName: $file->original_name,
            mimeType: $file->mime_type ?: 'application/octet-stream',
        );

        // $message is a danog\MadelineProto\EventHandler\Message object
        $messageId = $message->id;
        
        // Extract media info from the message object
        $media = $message->media ?? null;
        $fileId = null;
        $accessHash = null;
        $fileReference = null;
        $mimeType = $file->mime_type ?: 'application/octet-stream';
        $size = $file->size_bytes ?? 0;

        if ($media && method_exists($media, 'getDocument')) {
            $doc = $media->getDocument();
            $fileId = (string) ($doc->id ?? $messageId);
            $accessHash = $doc->access_hash ?? null;
            $fileReference = $doc->file_reference ?? null;
            $mimeType = $doc->mime_type ?? $mimeType;
            $size = $doc->size ?? $size;
        } elseif ($media && isset($media->document)) {
            $doc = $media->document;
            $fileId = (string) ($doc->id ?? $messageId);
        } else {
            // Fallback: use message ID as file reference
            $fileId = (string) $messageId;
        }

        return new TelegramUploadResult(
            chatId: 'me',
            messageId: $messageId,
            fileId: $fileId,
            meta: [
                'access_hash' => $accessHash,
                'file_reference' => $fileReference,
                'mime_type' => $mimeType,
                'size' => $size,
            ],
        );
    }
}
