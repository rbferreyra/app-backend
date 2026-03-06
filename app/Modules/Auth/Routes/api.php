<?php

use App\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Module Routes
|--------------------------------------------------------------------------
| Prefix: /auth (applied by AuthServiceProvider)
| Middleware: api (applied by AuthServiceProvider)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
