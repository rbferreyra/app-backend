<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\ChangePasswordDTO;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordAction
{
    /**
     * @throws ValidationException
     */
    public function execute(User $user, ChangePasswordDTO $dto): void
    {
        if (!Hash::check($dto->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($dto->password),
        ])->save();

        // Revoke all other tokens except current (force re-login on other devices)
        $user->tokens()
            ->where('id', '!=', $user->currentAccessToken()->id)
            ->delete();
    }
}
