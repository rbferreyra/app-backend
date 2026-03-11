<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Models\User;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class Verify2FAAction
{
    public function __construct(private Google2FA $google2fa)
    {
    }

    public function execute(User $user, string $code, string $deviceName): array
    {
        $validTotp = $this->google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $code
        );

        if (! $validTotp) {
            $recoveryCodes = $user->two_factor_recovery_codes ?? [];
            $index = array_search($code, $recoveryCodes);

            if ($index === false) {
                throw ValidationException::withMessages([
                    'code' => ['Invalid 2FA code.'],
                ]);
            }

            array_splice($recoveryCodes, $index, 1);
            $user->update(['two_factor_recovery_codes' => $recoveryCodes]);
        }

        // Revoga o token temporário de challenge
        $user->tokens()->where('name', '2fa-challenge')->delete();

        // Emite o token definitivo
        $token = $user->createToken($deviceName)->plainTextToken;

        event(new UserLoggedIn($user));

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
