<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriveFileController;
use App\Http\Controllers\PublicShareController;
use App\Http\Controllers\TelegramConnectController;
use Illuminate\Support\Facades\Route;

// Authentication Routes for Guests
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout Route (Auth required)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Share Routes (No authentication, no Telegram checks)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/share/{token}', [PublicShareController::class, 'show'])->name('drive.share.show');
    Route::get('/share/{token}/download', [PublicShareController::class, 'download'])->name('drive.share.download');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Telegram Connection/Onboarding Routes
    Route::prefix('connect')->name('telegram.')->group(function () {
        Route::get('/', [TelegramConnectController::class, 'show'])->name('show');
        Route::post('/', [TelegramConnectController::class, 'connect'])->name('connect');
        Route::get('/code', [TelegramConnectController::class, 'showCode'])->name('code.show');
        Route::post('/code', [TelegramConnectController::class, 'verifyCode'])->name('code.verify');
        Route::get('/password', [TelegramConnectController::class, 'showPassword'])->name('password.show');
        Route::post('/password', [TelegramConnectController::class, 'verifyPassword'])->name('password.verify');
    });

    // Dashboard & Drive (Telegram connection required)
    Route::middleware(['telegram_connected'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/files', [DriveFileController::class, 'index'])->name('drive.files.index');
        
        // Folder specific routes
        Route::get('/folders', [App\Http\Controllers\DriveFolderController::class, 'index'])->name('drive.folders.index');
        Route::get('/folders/{folder}', [App\Http\Controllers\DriveFolderController::class, 'show'])->name('drive.folders.show');
        Route::post('/folders', [DriveFileController::class, 'storeFolder'])->name('drive.folders.store');
        
        Route::post('/files', [DriveFileController::class, 'storeFile'])->name('drive.files.store');
        Route::get('/queue', [DriveFileController::class, 'queue'])->name('drive.queue');

        Route::get('/drive/files/{file}/download', [DriveFileController::class, 'download'])->name('drive.files.download');
        Route::get('/drive/files/{file}/preview', [DriveFileController::class, 'preview'])->name('drive.files.preview');
        Route::post('/drive/files/{file}/retry', [DriveFileController::class, 'retry'])->name('drive.files.retry');
        Route::delete('/drive/files/{file}', [DriveFileController::class, 'destroy'])->name('drive.files.destroy');
        Route::post('/drive/queue/clear', [DriveFileController::class, 'clearQueue'])->name('drive.queue.clear');
    });
});
