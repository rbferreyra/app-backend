<?php

use App\Modules\Notification\Controllers\NotificationPreferenceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification Module Routes
|--------------------------------------------------------------------------
| Prefix: /notification (applied by NotificationServiceProvider)
| Middleware: api (applied by NotificationServiceProvider)
|--------------------------------------------------------------------------
*/

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Notification preferences
    Route::get('/preferences', [NotificationPreferenceController::class, 'index'])
        ->name('notification.preferences.index');
    Route::put('/preferences', [NotificationPreferenceController::class, 'update'])
        ->name('notification.preferences.update');
});
