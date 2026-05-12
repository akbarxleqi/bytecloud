<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFiles = DriveFile::where('status', 'uploaded')->count();
        $totalSize = DriveFile::where('status', 'uploaded')->sum('size_bytes');
        $activeUploads = DriveFile::whereIn('status', ['pending', 'uploading'])->count();

        $stats = [
            [
                'label' => 'Total Files',
                'value' => number_format($totalFiles),
                'meta' => 'In Cloud',
                'icon' => 'description'
            ],
            [
                'label' => 'Storage Usage',
                'value' => $this->formatBytes($totalSize),
                'meta' => 'Telegram',
                'icon' => 'database'
            ],
            [
                'label' => 'Active Uploads',
                'value' => number_format($activeUploads),
                'meta' => 'Queue',
                'icon' => 'sync'
            ],
        ];

        $recentFiles = DriveFile::with('folder')
            ->where('status', 'uploaded')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->original_name,
                    'folder' => $file->folder?->name ?? 'Root',
                    'type' => strtoupper($file->extension ?? 'FILE'),
                    'mime_type' => $file->mime_type,
                    'size' => $file->human_size,
                    'status' => $file->status,
                    'icon' => $this->getFileIcon($file->mime_type),
                ];
            });

        return view('dashboard', [
            'stats' => $stats,
            'files' => $recentFiles,
        ]);
    }

    private function formatBytes($bytes, $precision = 1)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getFileIcon($mimeType)
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'image',
            str_starts_with($mimeType, 'video/') => 'movie',
            str_starts_with($mimeType, 'audio/') => 'audio_file',
            $mimeType === 'application/pdf' => 'picture_as_pdf',
            str_contains($mimeType, 'zip') || str_contains($mimeType, 'rar') => 'folder_zip',
            default => 'description',
        };
    }
}
