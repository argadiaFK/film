<?php

use App\Http\Controllers\BrowseController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeriesController;
use App\Models\Ad;
use Illuminate\Support\Facades\Route;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/film/{slug}', [FilmController::class, 'show'])->name('film.show');
Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/series/{slug}/season/{season}/episode/{episode}', [SeriesController::class, 'episode'])->name('series.episode');
Route::get('/browse', [BrowseController::class, 'index'])->name('browse');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/api/search', [App\Http\Controllers\Api\SearchController::class, 'search'])->name('api.search');
Route::post('/api/comments', [App\Http\Controllers\Api\CommentController::class, 'store'])->name('api.comments.store');

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
