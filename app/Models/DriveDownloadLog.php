<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriveDownloadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'drive_file_id',
        'ip_address',
        'user_agent',
        'download_type',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(DriveFile::class, 'drive_file_id');
    }
}
