<?php

namespace App\Http\Controllers;

use App\Jobs\UploadFileToTelegramJob;
use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Services\Drive\DriveDownloadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DriveFileController extends Controller
{
    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:drive_folders,id',
        ]);

        $user = auth()->user();
        $accountId = $user->telegramAccount?->id;

        if ($request->parent_id) {
            $parent = DriveFolder::where('telegram_account_id', $accountId)->findOrFail($request->parent_id);
        }

        DriveFolder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'telegram_account_id' => $accountId,
        ]);

        return back()->with('status', 'Folder created successfully.');
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:'.(config('drive.max_upload_mb') * 1024),
            'folder_id' => 'nullable|exists:drive_folders,id',
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $mimeType = $uploadedFile->getMimeType();
        $sizeBytes = $uploadedFile->getSize();

        $path = $uploadedFile->store(config('drive.tmp_path'), config('drive.tmp_disk'));
        \Illuminate\Support\Facades\Log::debug('Upload store result', [
            'path' => $path,
            'tmp_disk' => config('drive.tmp_disk'),
            'tmp_path' => config('drive.tmp_path'),
            'upload_error' => $uploadedFile->getError(),
            'upload_error_msg' => $uploadedFile->getErrorMessage(),
            'orig_name' => $originalName,
            'size' => $sizeBytes,
        ]);
        $user = auth()->user();
        $account = $user->telegramAccount;

        if ($request->folder_id) {
            $folder = DriveFolder::where('telegram_account_id', $account?->id)->findOrFail($request->folder_id);
        }

        $file = DriveFile::create([
            'folder_id' => $request->folder_id,
            'telegram_account_id' => $account?->id,
            'original_name' => $originalName,
            'stored_name' => $path ? basename($path) : '',
            'extension' => $extension,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'tmp_path' => $path ?: null,
            'status' => 'pending',
        ]);

        UploadFileToTelegramJob::dispatch($file->id);

        return back()->with('status', 'File upload queued.');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $accountId = $user->telegramAccount?->id;

        $files = DriveFile::with('folder')
            ->where('telegram_account_id', $accountId)
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

        return view('files-all', [
            'files' => $files,
        ]);
    }

    public function queue()
    {
        $user = auth()->user();
        $accountId = $user->telegramAccount?->id;

        $uploads = DriveFile::where('telegram_account_id', $accountId)
            ->whereIn('status', ['pending', 'uploading', 'failed'])
            ->latest()
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->original_name,
                    'size' => $file->human_size,
                    'progress' => $file->status === 'uploaded' ? 100 : ($file->status === 'failed' ? 0 : $file->upload_progress),
                    'speed' => $file->status === 'uploading' ? 'Uploading...' : 'Pending...',
                    'icon' => $this->getFileIcon($file->mime_type),
                    'status' => $file->status,
                    'error' => $file->error_message,
                ];
            });

        return view('queue', [
            'uploads' => $uploads,
        ]);
    }

    public function download(DriveFile $file, DriveDownloadService $downloadService)
    {
        abort_unless($file->telegram_account_id === auth()->user()->telegramAccount?->id, 403);
        return $downloadService->stream($file);
    }

    public function preview(DriveFile $file, DriveDownloadService $downloadService)
    {
        abort_unless($file->telegram_account_id === auth()->user()->telegramAccount?->id, 403);
        return $downloadService->stream($file);
    }

    public function retry(DriveFile $file): RedirectResponse
    {
        abort_unless($file->telegram_account_id === auth()->user()->telegramAccount?->id, 403);
        abort_unless($file->status === 'failed', 409);

        $file->update(['status' => 'pending', 'error_message' => null]);
        UploadFileToTelegramJob::dispatch($file->id);

        return back()->with('status', 'Upload queued again.');
    }

    public function destroy(DriveFile $file): RedirectResponse
    {
        abort_unless($file->telegram_account_id === auth()->user()->telegramAccount?->id, 403);
        if ($file->tmp_path) {
            Storage::disk(config('drive.tmp_disk'))->delete($file->tmp_path);
        }

        $file->delete();

        return back()->with('status', 'File removed.');
    }

    public function clearQueue(): RedirectResponse
    {
        $user = auth()->user();
        $accountId = $user->telegramAccount?->id;

        $files = DriveFile::where('telegram_account_id', $accountId)
            ->whereIn('status', ['uploaded', 'failed'])->get();

        foreach ($files as $file) {
            if ($file->tmp_path) {
                Storage::disk(config('drive.tmp_disk'))->delete($file->tmp_path);
            }
            // If we want to keep uploaded files in the main list, we should only delete 'failed' ones
            // but the user asked about 'upload failed' specifically.
            // Let's only delete failed ones for safety, or both if they are in 'queue' view.
            // Usually 'queue' shows all. Let's delete failed ones.
            if ($file->status === 'failed') {
                $file->delete();
            }
        }

        return back()->with('status', 'Queue cleared.');
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
