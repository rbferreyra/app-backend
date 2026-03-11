<?php

namespace App\Modules\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireTwoFactorChallenge
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->user()?->tokenCan('2fa-challenge')) {
            return response()->json([
                'message' => 'This action requires a 2FA challenge token.',
                'status' => 403,
            ], 403);
        }

        return $next($request);
    }
}
