<?php

use App\Http\Controllers\Backend\Auth\AdminLoginController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\ClientController;
use App\Http\Controllers\Backend\ClientNoteController;
use App\Http\Controllers\Backend\ContactMessageController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\GalleryItemController;
use App\Http\Controllers\Backend\LocationController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\ServiceCategoryController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\SiteSettingController;
use App\Http\Controllers\Backend\StaffManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store']);
    });

    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        // Admin Dashboard Route
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('staff', StaffManagementController::class);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('categories', ServiceCategoryController::class);
        Route::resource('services', ServiceController::class);
        Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status.update');
        Route::resource('bookings', BookingController::class);

        // Client CRM Routes
        Route::resource('clients', ClientController::class);

        // Client Notes Routes
        Route::post('clients/notes', [ClientNoteController::class, 'store'])->name('clients.notes.store');
        Route::put('clients/notes/{clientNote}', [ClientNoteController::class, 'update'])->name('clients.notes.update');
        Route::delete('clients/notes/{clientNote}', [ClientNoteController::class, 'destroy'])->name('clients.notes.destroy');
        Route::resource('locations', LocationController::class)->except(['show']);

        // Internal API Routes for Bookings
        Route::get('bookings/api/search-clients', [BookingController::class, 'searchClients'])->name('bookings.api.clients');
        Route::get('bookings/api/locations/{location}/staff', [BookingController::class, 'getStaffByLocation'])->name('bookings.api.staff');

        // Site Settings
        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');

        // Static Pages (CMS)
        Route::resource('pages', PageController::class);

        // Content Modules
        Route::resource('posts', PostController::class);
        Route::resource('gallery', GalleryItemController::class)->parameters(['gallery' => 'galleryItem']);
        Route::resource('faq', FaqController::class);

        // Contact Messages Inbox
        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{message}/mark-as-read', [ContactMessageController::class, 'markAsRead'])->name('messages.mark-as-read');
        Route::post('/messages/{message}/reply', [ContactMessageController::class, 'reply'])->name('messages.reply');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        // Review Admin Routes
        Route::resource('reviews', ReviewController::class);
    });
});
