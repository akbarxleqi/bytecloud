<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use Illuminate\Http\Request;

class DriveFolderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $accountId = $user->telegramAccount?->id;

        $folders = $accountId ? DriveFolder::where('telegram_account_id', $accountId)
            ->withCount(['files' => function ($query) use ($accountId) {
                $query->where('telegram_account_id', $accountId);
            }])
            ->whereNull('parent_id')
            ->get()
            ->map(function ($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'items' => $folder->files_count,
                    'size' => $this->formatBytes($folder->files()->where('telegram_account_id', $folder->telegram_account_id)->sum('size_bytes')),
                    'icon' => 'folder',
                ];
            }) : collect();

        return view('folders.index', [
            'folders' => $folders,
        ]);
    }

    public function show(DriveFolder $folder, Request $request)
    {
        $user = auth()->user();
        $accountId = $user->telegramAccount?->id;

        abort_unless($folder->telegram_account_id === $accountId, 403);

        $breadcrumbs = [];
        $temp = $folder;
        while ($temp) {
            array_unshift($breadcrumbs, ['id' => $temp->id, 'name' => $temp->name]);
            $temp = $temp->parent;
        }

        $folders = DriveFolder::where('telegram_account_id', $accountId)
            ->withCount(['files' => function ($query) use ($accountId) {
                $query->where('telegram_account_id', $accountId);
            }])
            ->where('parent_id', $folder->id)
            ->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'name' => $f->name,
                    'items' => $f->files_count,
                    'size' => $this->formatBytes($f->files()->where('telegram_account_id', $f->telegram_account_id)->sum('size_bytes')),
                    'icon' => 'folder',
                ];
            });

        $files = DriveFile::with('folder')
            ->where('telegram_account_id', $accountId)
            ->where('folder_id', $folder->id)
            ->when($request->search, function ($query, $search) {
                $query->where('original_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->through(function ($file) {
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

        return view('folders.show', [
            'folder' => $folder,
            'folders' => $folders,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    private function getFileIcon(?string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'image',
            str_starts_with($mimeType, 'video/') => 'movie',
            str_starts_with($mimeType, 'audio/') => 'audio_file',
            $mimeType === 'application/pdf' => 'picture_as_pdf',
            str_contains($mimeType, 'zip') || str_contains($mimeType, 'compressed') => 'folder_zip',
            default => 'description',
        };
    }

    private function formatBytes($bytes, $precision = 1)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
