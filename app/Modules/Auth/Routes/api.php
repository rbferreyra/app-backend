<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Controllers\DeviceController;
use App\Modules\Auth\Controllers\EmailVerificationController;
use App\Modules\Auth\Controllers\PasswordController;
use App\Modules\Auth\Controllers\ProfileController;
use App\Modules\Auth\Controllers\TwoFactorController;
use App\Modules\Auth\Middleware\RequireTwoFactorChallenge;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Module Routes
|--------------------------------------------------------------------------
| Prefix: /auth (applied by AuthServiceProvider)
| Middleware: api (applied by AuthServiceProvider)
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Email verification
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

// Password recovery (public)
Route::post('/password/forgot', [PasswordController::class, 'forgot'])->name('auth.password.forgot');
Route::post('/password/reset', [PasswordController::class, 'reset'])->name('auth.password.reset');

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/logout/all', [AuthController::class, 'logoutAll'])->name('auth.logout.all');

    // Email verification
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('auth.email.resend');

    // Password
    Route::put('/password/change', [PasswordController::class, 'change'])->name('auth.password.change');

    // Profile
    Route::get('/me', [ProfileController::class, 'me'])->name('auth.me');
    Route::put('/profile', [ProfileController::class, 'update'])->name('auth.profile.update');

    // 2FA
    Route::prefix('2fa')->group(function () {
        Route::post('/verify', [TwoFactorController::class, 'verify'])
            ->middleware(RequireTwoFactorChallenge::class)
            ->name('auth.2fa.verify');

        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('auth.2fa.enable');
        Route::post('/confirm', [TwoFactorController::class, 'confirm'])->name('auth.2fa.confirm');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('auth.2fa.disable');
        Route::get('/recovery-codes', [TwoFactorController::class, 'recoveryCodes'])->name('auth.2fa.recovery-codes');
        Route::post('/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('auth.2fa.recovery-codes.regenerate');
    });

    // Devices
    Route::prefix('devices')->group(function () {
        Route::get('/', [DeviceController::class, 'index'])->name('auth.devices.index');
        Route::delete('/', [DeviceController::class, 'destroyAll'])->name('auth.devices.destroy-all');
        Route::delete('/{uuid}', [DeviceController::class, 'destroy'])->name('auth.devices.destroy');
    });
});
