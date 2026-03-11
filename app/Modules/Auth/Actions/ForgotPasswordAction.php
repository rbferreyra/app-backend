<?php

namespace App\Modules\Auth\Actions;

use Illuminate\Support\Facades\Password;

class ForgotPasswordAction
{
    public function execute(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }
}
