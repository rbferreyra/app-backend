<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class VerifyEmailAction
{
    /**
     * @throws AuthorizationException
     */
    public function execute(User $user, string $hash): void
    {
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->markEmailAsVerified();
    }
}
