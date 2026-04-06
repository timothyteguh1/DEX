<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Middleware\CheckUserStatus;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

// =============================================
// SERVE STORAGE (fallback jika storage:link gagal)
// =============================================
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.serve');

// =============================================
// GUEST ROUTES
// =============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

// =============================================
// EMAIL VERIFICATION ROUTES
// =============================================
Route::middleware('auth')->group(function () {

    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', '✅ Email berhasil diverifikasi! Akun Anda sedang menunggu persetujuan Admin.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/resend', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:3,1')
        ->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// =============================================
// USER ROUTES (login + approved)
// =============================================
Route::middleware(['auth', CheckUserStatus::class])->group(function () {
    Route::get('/', [CoinController::class, 'index'])->name('dashboard');
    Route::get('/discover', [CoinController::class, 'discover'])->name('discover');
    Route::post('/add-coin', [CoinController::class, 'store'])->name('add-coin');
    Route::delete('/remove-coin/{coin}', [CoinController::class, 'destroy'])->name('remove-coin');
    Route::post('/toggle-active/{coin}', [CoinController::class, 'toggleActive'])->name('toggle-active');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// =============================================
// ADMIN ROUTES (admin + superadmin)
// =============================================
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::put('/user/{user}/status', [AdminController::class, 'updateStatus'])->name('admin.user.status');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

    // --- REFERRAL CODE (Admin & Superadmin bisa akses) ---
    Route::get('/referral-codes', [AdminController::class, 'referralCodes'])->name('admin.referral-codes');
    Route::post('/referral-codes', [AdminController::class, 'storeReferralCode'])->name('admin.referral-codes.store');
    Route::put('/referral-codes/{referralCode}/toggle', [AdminController::class, 'toggleReferralCode'])->name('admin.referral-codes.toggle');
    Route::delete('/referral-codes/{referralCode}', [AdminController::class, 'destroyReferralCode'])->name('admin.referral-codes.destroy');

    // --- LAPORAN MEMBER (Admin & Superadmin bisa akses) ---
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
});

// =============================================
// SUPERADMIN ROUTES
// =============================================
Route::middleware(['auth', IsSuperAdmin::class])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
    Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('user.destroy');
    Route::put('/users/{user}/reset-password', [SuperAdminController::class, 'resetPassword'])->name('user.reset_password');

    // --- KELOLA ADMIN (Hanya Superadmin) ---
    Route::get('/admins', [SuperAdminController::class, 'manageAdmins'])->name('admins');
    Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('admins.store');
    Route::put('/admins/{user}', [SuperAdminController::class, 'updateAdmin'])->name('admins.update');
});