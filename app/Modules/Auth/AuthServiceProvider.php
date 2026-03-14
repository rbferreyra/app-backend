<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Events\EmailChanged;
use App\Modules\Auth\Events\PasswordChanged;
use App\Modules\Auth\Events\TwoFactorDisabled;
use App\Modules\Auth\Events\TwoFactorEnabled;
use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Auth\Listeners\LogUserLoggedIn;
use App\Modules\Auth\Listeners\SendVerificationEmail;
use App\Modules\Auth\Models\PersonalAccessToken;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
use App\Modules\Notification\Listeners\CreateDefaultNotificationPreferencesListener;
use App\Modules\Notification\Listeners\SendSystemNotification;
use App\Shared\Providers\ModuleServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

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

        // Auth events
        Event::listen(UserRegistered::class, SendVerificationEmail::class);
        Event::listen(UserLoggedIn::class, LogUserLoggedIn::class);
        Event::listen(UserLoggedIn::class, SendSystemNotification::class);
        Event::listen(PasswordChanged::class, SendSystemNotification::class);
        Event::listen(EmailChanged::class, SendSystemNotification::class);
        Event::listen(TwoFactorEnabled::class, SendSystemNotification::class);
        Event::listen(TwoFactorDisabled::class, SendSystemNotification::class);

        // Create default notification preferences for new users
        Event::listen(UserRegistered::class, CreateDefaultNotificationPreferencesListener::class);

        // Reset password email
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url')
                . '/reset-password?token=' . $token
                . '&email=' . urlencode($user->email);
        });

        // Sanctum personal access token model
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
