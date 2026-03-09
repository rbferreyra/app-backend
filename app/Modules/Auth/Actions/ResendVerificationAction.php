<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;

class ResendVerificationAction
{
    public function execute(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
