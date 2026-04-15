<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    LoginController,
    ProfileController,
    SnifferController,
    UserController
};

// --- Halaman Public ---
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'process'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/reset-password', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');

// --- Halaman Private (Harus Login) ---
Route::middleware(['auth'])->group(function () {
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getLiveStats'])->name('dashboard.stats');
    Route::get('/dashboard/interface-history', [DashboardController::class, 'getInterfaceHistory'])->name('interface.history');
    //profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/sniffer/active', [SnifferController::class, 'active'])->name('sniffer.active');
    Route::get('/sniffer/history', [SnifferController::class, 'history'])->name('sniffer.history');
    Route::get('/sniffer/api', [SnifferController::class, 'api'])->name('sniffer.api');

    // Redirect /sniffer ke /sniffer/active biar ga 404
    Route::get('/sniffer', fn() => redirect()->route('sniffer.active'));

    Route::middleware(['permission:manage'])->group(function () {

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    });

});