<?php

return [
    'tmp_disk' => env('DRIVE_TMP_DISK', 'local'),
    'tmp_path' => env('DRIVE_TMP_PATH', 'tmp'),
    'preview_path' => env('DRIVE_PREVIEW_PATH', 'previews'),
    'thumbnail_path' => env('DRIVE_THUMBNAIL_PATH', 'thumbnails'),
    'telegram_session_path' => env('TELEGRAM_SESSION_PATH', 'telegram-sessions'),
    'max_upload_mb' => (int) env('DRIVE_MAX_UPLOAD_MB', 2048),
    'default_visibility' => env('DRIVE_DEFAULT_VISIBILITY', 'private'),
];
