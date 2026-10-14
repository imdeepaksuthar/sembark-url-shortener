<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Short URL redirection
Route::get('/r/{code}', [RedirectController::class, 'show'])
    ->where('code', '[A-Za-z0-9]+')
    ->name('short-urls.redirect');

// Invitation acceptance (accessible by guests and logged-in users)
Route::controller(InvitationController::class)->group(function () {
    Route::get('/invitations/{token}', 'show')->name('invitations.accept.form');
    Route::post('/invitations/{token}', 'accept')->name('invitations.accept');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Short URLs
    Route::get('/short-urls/export', [ShortUrlController::class, 'export'])
        ->name('short-urls.export');

    Route::resource('short-urls', ShortUrlController::class)
        ->only(['index', 'create', 'store']);

    // Company invitation (Super Admin only)
    Route::middleware('can:manage-companies')
        ->post('/admin/company-invitations', [InvitationController::class, 'inviteCompanyAdmin'])
        ->name('invitations.company-admin');

    // Team invitation (Company Admin only)
    Route::middleware('can:manage-team')
        ->post('/team-invitations', [InvitationController::class, 'inviteTeamMember'])
        ->name('invitations.team');
});