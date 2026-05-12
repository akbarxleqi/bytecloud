<?php

namespace App\Http\Controllers;

use App\Models\DriveShareLink;
use App\Services\Drive\DriveDownloadService;
use Illuminate\View\View;

class PublicShareController extends Controller
{
    public function show(string $token): View
    {
        $share = DriveShareLink::with('file.folder')->where('token', $token)->firstOrFail();

        abort_unless($share->isAvailable(), 404);

        return view('share.show', ['share' => $share]);
    }

    public function download(string $token, DriveDownloadService $downloadService)
    {
        $share = DriveShareLink::with('file')->where('token', $token)->firstOrFail();

        abort_unless($share->isAvailable(), 404);

        $share->increment('download_count');

        return $downloadService->stream($share->file, 'share');
    }
}
