<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriveFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'folder_id',
        'telegram_account_id',
        'original_name',
        'stored_name',
        'extension',
        'mime_type',
        'size_bytes',
        'checksum',
        'telegram_chat_id',
        'telegram_message_id',
        'telegram_file_id',
        'telegram_meta',
        'tmp_path',
        'preview_path',
        'thumbnail_path',
        'visibility',
        'status',
        'upload_progress',
        'notes',
        'is_favorite',
        'uploaded_at',
        'preview_generated_at',
        'error_message',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'telegram_meta' => 'array',
            'meta' => 'array',
            'is_favorite' => 'boolean',
            'uploaded_at' => 'datetime',
            'preview_generated_at' => 'datetime',
        ];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DriveFolder::class, 'folder_id');
    }

    public function telegramAccount(): BelongsTo
    {
        return $this->belongsTo(TelegramAccount::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(DriveTag::class, 'drive_file_tag');
    }

    public function shareLinks(): HasMany
    {
        return $this->hasMany(DriveShareLink::class);
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DriveDownloadLog::class);
    }

    public function getHumanSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = max(0, $this->size_bytes);

        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $i === 0 ? 0 : 1).' '.$units[$i];
    }
}
