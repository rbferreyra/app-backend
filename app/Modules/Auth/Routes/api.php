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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verification
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

// Password recovery (public)
Route::post('/password/forgot', [PasswordController::class, 'forgot']);
Route::post('/password/reset', [PasswordController::class, 'reset']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout/all', [AuthController::class, 'logoutAll']);

    // Email verification
    Route::post('/email/resend', [EmailVerificationController::class, 'resend']);

    // Password
    Route::put('/password/change', [PasswordController::class, 'change']);

    // Profile
    Route::get('/me', [ProfileController::class, 'me']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // 2FA
    Route::prefix('2fa')->group(function () {
        // Só acessível com token temporário de 2fa-challenge
        Route::post('/verify', [TwoFactorController::class, 'verify'])
            ->middleware(RequireTwoFactorChallenge::class);

        // Exigem token completo (sem ability restrita)
        Route::post('/enable', [TwoFactorController::class, 'enable']);
        Route::post('/confirm', [TwoFactorController::class, 'confirm']);
        Route::post('/disable', [TwoFactorController::class, 'disable']);
        Route::get('/recovery-codes', [TwoFactorController::class, 'recoveryCodes']);
        Route::post('/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes']);
    });

    // Devices
    Route::prefix('devices')->group(function () {
        Route::get('/', [DeviceController::class, 'index']);
        Route::delete('/', [DeviceController::class, 'destroyAll']);
        Route::delete('/{uuid}', [DeviceController::class, 'destroy']);
    });
});
