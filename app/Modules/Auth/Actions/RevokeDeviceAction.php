<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use App\Shared\Exceptions\ModelNotFoundException;

class RevokeDeviceAction
{
    public function execute(User $user, int $deviceId): void
    {
        $token = $user
            ->tokens()
            ->where('id', $deviceId)
            ->first();

        if (! $token) {
            throw new ModelNotFoundException('Device not found.');
        }

        $token->delete();
    }
}
