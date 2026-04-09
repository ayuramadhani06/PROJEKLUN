<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    LoginController,
    ProfileController,
    SnifferController
};

// --- Halaman Public ---
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'process'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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

   Route::get('/sniffer', [SnifferController::class, 'index'])->name('sniffer.index');
    Route::get('/sniffer/aoi', [SnifferController::class, 'api'])->name('sniffer.api');

});