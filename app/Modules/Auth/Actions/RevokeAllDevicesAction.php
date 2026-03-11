<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;

class RevokeAllDevicesAction
{
    public function execute(User $user): void
    {
        $currentTokenId = $user->currentAccessToken()->id;

        $user->tokens()
            ->where('id', '!=', $currentTokenId)
            ->where('name', '!=', '2fa-challenge')
            ->delete();
    }
}
