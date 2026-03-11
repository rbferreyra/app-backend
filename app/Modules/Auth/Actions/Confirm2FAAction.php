<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class Confirm2FAAction
{
    public function __construct(private Google2FA $google2fa)
    {
    }

    public function execute(User $user, string $code): array
    {
        if (!$user->two_factor_secret) {
            throw ValidationException::withMessages([
                'code' => ['2FA não foi iniciado. Chame /2fa/enable primeiro.'],
            ]);
        }

        $valid = $this->google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $code
        );

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['Código TOTP inválido.'],
            ]);
        }

        $recoveryCodes = collect(range(1, 8))
            ->map(fn() => Str::upper(Str::random(5) . '-' . Str::random(5)))
            ->all();

        $user->update([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        return ['recovery_codes' => $recoveryCodes];
    }
}
