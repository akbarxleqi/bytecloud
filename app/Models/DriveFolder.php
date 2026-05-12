<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DriveFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'path',
        'description',
        'color',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::saving(function (DriveFolder $folder): void {
            $folder->slug = $folder->slug ?: Str::slug($folder->name);

            $parentPath = $folder->parent?->path;
            $folder->path = trim(($parentPath ? $parentPath.'/' : '').$folder->slug, '/');
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DriveFile::class, 'folder_id');
    }
}
