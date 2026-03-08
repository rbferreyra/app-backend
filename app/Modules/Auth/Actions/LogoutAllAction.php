<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;

class LogoutAllAction
{
    public function execute(User $user): void
    {
        // Revoke all tokens from all devices
        $user->tokens()->delete();
    }
}
