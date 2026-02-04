<?php

use App\Http\Controllers\DownloadController;
use App\Models\Ad;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Download tracking route - increments click count and redirects
Route::get('/download/{downloadLink}', [DownloadController::class, 'track'])
    ->name('download.track');

// Ad click tracking route
Route::get('/ad/click/{ad}', function (Ad $ad) {
    $ad->trackClick();
    return redirect($ad->link ?? '/');
})->name('ad.click');

// Backup download route (admin only)
Route::get('/admin/backup-manager/download/{filename}', function (string $filename) {
    $path = storage_path('app/backups/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->download($path, $filename, [
        'Content-Type' => 'application/octet-stream',
    ]);
})->middleware(['auth'])->name('filament.admin.pages.backup-manager.download');
