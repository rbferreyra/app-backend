<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class RegenerateRecoveryCodesAction
{
    public function execute(User $user): array
    {
        if (!$user->hasTwoFactorEnabled()) {
            throw ValidationException::withMessages([
                'two_factor' => ['2FA não está ativo.'],
            ]);
        }

        $recoveryCodes = collect(range(1, 8))
            ->map(fn() => Str::upper(Str::random(5) . '-' . Str::random(5)))
            ->all();

        $user->update(['two_factor_recovery_codes' => $recoveryCodes]);

        return $recoveryCodes;
    }
}
