<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DriveTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color'];

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(DriveFile::class, 'drive_file_tag');
    }
}
