<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GalleryAlbumController as AdminGalleryAlbumController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\MusicSheetController as AdminMusicSheetController;
use App\Http\Controllers\Admin\PerformanceController as AdminPerformanceController;
use App\Http\Controllers\Admin\SlideshowController as AdminSlideshowController;
use App\Http\Controllers\MusicSheetPublicController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return redirect()->route('admin.login.form');
})->name('login');

Route::get('/', [SiteController::class, 'home'])->name('site.home');
Route::get('/history', [SiteController::class, 'history'])->name('site.history');
Route::get('/events', [SiteController::class, 'events'])->name('site.events');
Route::get('/gallery', [SiteController::class, 'gallery'])->name('site.gallery');
Route::get('/gallery/{album}', [SiteController::class, 'galleryAlbum'])->name('site.gallery.album');
Route::get('/performances', [SiteController::class, 'performances'])->name('site.performances');
Route::get('/members', [SiteController::class, 'members'])->name('site.members');
Route::get('/contact', [SiteController::class, 'contact'])->name('site.contact');
Route::get('/music-sheets', [SiteController::class, 'musicSheets'])->name('site.music_sheets');
Route::post('/music-sheets/{music_sheet}/track-view', [MusicSheetPublicController::class, 'trackView'])->name('site.music_sheets.track_view');
Route::post('/music-sheets/{music_sheet}/download-intent', [MusicSheetPublicController::class, 'downloadIntent'])->name('site.music_sheets.download_intent');
Route::get('/music-sheets/{music_sheet}/file', [MusicSheetPublicController::class, 'file'])->name('site.music_sheets.file');
Route::get('/music-sheets/{music_sheet}/download', [MusicSheetPublicController::class, 'download'])->name('site.music_sheets.download');
Route::get('/music-sheets/{music_sheet}/download-file', [MusicSheetPublicController::class, 'downloadFile'])->name('site.music_sheets.download_file');

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('admin.members.index');
        }

        return redirect()->route('admin.login.form');
    })->name('admin.home');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth')->name('admin.logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::prefix('api')->group(function () {
            Route::get('/roles', [LookupController::class, 'rolesIndex'])->name('admin.api.roles.index');
            Route::post('/roles', [LookupController::class, 'rolesStore'])->name('admin.api.roles.store');
            Route::get('/voice-parts', [LookupController::class, 'voicePartsIndex'])->name('admin.api.voice_parts.index');
            Route::post('/voice-parts', [LookupController::class, 'voicePartsStore'])->name('admin.api.voice_parts.store');
        });

        Route::resource('members', AdminMemberController::class)
            ->names('admin.members')
            ->except(['show']);

        Route::resource('events', AdminEventController::class)
            ->names('admin.events')
            ->except(['show']);

        Route::post('gallery-albums/{gallery_album}/photos/reorder', [AdminGalleryAlbumController::class, 'reorderPhotos'])
            ->name('admin.gallery_albums.photos.reorder');
        Route::delete('gallery-albums/{gallery_album}/photos/{photo}', [AdminGalleryAlbumController::class, 'destroyPhoto'])
            ->name('admin.gallery_albums.photos.destroy');

        Route::resource('gallery-albums', AdminGalleryAlbumController::class)
            ->names('admin.gallery_albums')
            ->except(['show']);

        Route::resource('performances', AdminPerformanceController::class)
            ->names('admin.performances')
            ->except(['show']);

        Route::get('slideshow', [AdminSlideshowController::class, 'index'])->name('admin.slideshow.index');
        Route::post('slideshow', [AdminSlideshowController::class, 'store'])->name('admin.slideshow.store');
        Route::post('slideshow/bulk-delete', [AdminSlideshowController::class, 'bulkDestroy'])->name('admin.slideshow.bulk_delete');
        Route::delete('slideshow/{slideshow_image}', [AdminSlideshowController::class, 'destroy'])->name('admin.slideshow.destroy');

        Route::get('music-sheets/analytics', [AdminMusicSheetController::class, 'analytics'])
            ->name('admin.music_sheets.analytics');

        Route::resource('music-sheets', AdminMusicSheetController::class)
            ->names('admin.music_sheets')
            ->except(['show']);
    });
});
