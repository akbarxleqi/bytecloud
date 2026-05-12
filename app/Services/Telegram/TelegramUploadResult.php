<?php

namespace App\Services\Telegram;

final readonly class TelegramUploadResult
{
    public function __construct(
        public string $chatId,
        public int $messageId,
        public ?string $fileId = null,
        public array $meta = [],
    ) {}
}
