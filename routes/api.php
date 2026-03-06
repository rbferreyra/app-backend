<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes are prefixed with /api (defined in bootstrap/app.php)
| Module routes are loaded by their respective ServiceProviders
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'message' => 'API is running',
        'version' => config('app.version', '1.0.0'),
    ]);
});
