<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_id',
        'api_hash',
        'phone_number',
        'session_name',
        'session_path',
        'default_chat_type',
        'default_chat_id',
        'is_connected',
        'last_connected_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_connected' => 'boolean',
            'last_connected_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    protected function apiHash(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? decrypt($value) : null,
            set: fn (?string $value) => $value ? encrypt($value) : null,
        );
    }

    public function files(): HasMany
    {
        return $this->hasMany(DriveFile::class);
    }
}
