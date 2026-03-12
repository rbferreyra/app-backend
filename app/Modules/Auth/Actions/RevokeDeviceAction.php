<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use App\Shared\Exceptions\ModelNotFoundException;

class RevokeDeviceAction
{
    public function execute(User $user, string $uuid): void
    {
        $token = $user
            ->tokens()
            ->where('uuid', $uuid)
            ->first();

        if (! $token) {
            throw new ModelNotFoundException('Device not found.');
        }

        $token->delete();
    }
}
