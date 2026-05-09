<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Public\SearchController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [SearchController::class, 'index'])->name('search');
Route::get('/search', [SearchController::class, 'results'])->name('search.results');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Public admin routes (login/logout)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });
});
