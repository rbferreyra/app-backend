<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Auth\Listeners\SendVerificationEmail;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use App\Shared\Providers\ModuleServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;

class AuthServiceProvider extends ModuleServiceProvider
{
    protected function routeFilePath(): string
    {
        return __DIR__ . '/Routes/api.php';
    }

    protected function routePrefix(): string
    {
        return 'auth';
    }

    protected function bindings(): array
    {
        return [
            UserRepositoryInterface::class => UserRepository::class,
        ];
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen(
            UserRegistered::class,
            SendVerificationEmail::class,
        );

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url')
                . '/reset-password?token=' . $token
                . '&email=' . urlencode($user->email);
        });
    }
}
