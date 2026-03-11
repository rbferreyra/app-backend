<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\ResetPasswordDTO;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordAction
{
    /**
     * @throws ValidationException
     */
    public function execute(ResetPasswordDTO $dto): void
    {
        $status = Password::reset(
            [
                'email' => $dto->email,
                'token' => $dto->token,
                'password' => $dto->password,
                'password_confirmation' => $dto->password,
            ],
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])
                    ->save();

                // Revoke all tokens after password reset (security)
                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
