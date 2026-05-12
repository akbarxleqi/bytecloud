<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('components.layouts.bytecloud', function ($view) {
            $totalSize = \App\Models\DriveFile::where('status', 'uploaded')->sum('size_bytes');
            $maxSize = 214748364800; // 200 GB placeholder
            $percentage = $maxSize > 0 ? round(($totalSize / $maxSize) * 100) : 0;

            $account = \App\Models\TelegramAccount::first();

            $view->with([
                'storageUsed' => $this->formatBytes($totalSize),
                'storagePercentage' => min($percentage, 100),
                'telegramAccount' => $account,
            ]);
        });
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
}
