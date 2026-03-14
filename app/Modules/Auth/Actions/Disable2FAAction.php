<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Events\TwoFactorDisabled;
use App\Modules\Auth\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Disable2FAAction
{
    public function __construct(
        private readonly Request $request,
    ) {
    }

    public function execute(User $user, string $password): void
    {
        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Senha incorreta.'],
            ]);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        event(new TwoFactorDisabled(
            user: $user,
            ip: $this->request->ip(),
        ));
    }
}
