<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\CheckUserStatus; 

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
     Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

Route::middleware(['auth', CheckUserStatus::class])->group(function () {
    Route::get('/', [CoinController::class, 'index'])->name('dashboard'); 
    Route::get('/discover', [CoinController::class, 'discover'])->name('discover');
    Route::post('/add-coin', [CoinController::class, 'store'])->name('add-coin');
    Route::delete('/remove-coin/{coin}', [CoinController::class, 'destroy'])->name('remove-coin');
    
    // RUTE BARU: Sinkronisasi state layar dari JS ke Database
    Route::post('/toggle-active/{coin}', [CoinController::class, 'toggleActive'])->name('toggle-active');
    // ---> TAMBAHKAN RUTE INI:
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::put('/user/{user}/status', [AdminController::class, 'updateStatus'])->name('admin.user.status');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});