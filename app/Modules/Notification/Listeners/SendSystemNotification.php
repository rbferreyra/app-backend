<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Auth\Events\EmailChanged;
use App\Modules\Auth\Events\PasswordChanged;
use App\Modules\Auth\Events\TwoFactorDisabled;
use App\Modules\Auth\Events\TwoFactorEnabled;
use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Notification\Actions\SendNotificationAction;
use App\Modules\Notification\DTOs\NotificationDTO;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSystemNotification implements ShouldQueue
{
    public function __construct(
        private readonly SendNotificationAction $action,
    ) {
    }

    public function handle(object $event): void
    {
        $notifiable = $event->user ?? $event->notifiable ?? null;

        if (! $notifiable) {
            return;
        }

        $dto = $this->resolveDTO($event);

        if (! $dto) {
            return;
        }

        $this->action->execute($notifiable, $dto);
    }

    private function resolveDTO(object $event): ?NotificationDTO
    {
        return match (get_class($event)) {
            UserLoggedIn::class => new NotificationDTO(
                type:     'auth.login',
                subject:  'New login detected',
                template: 'notification::emails.auth.login-detected',
                data: [
                    'name'      => $event->user->name,
                    'ip'        => $event->ip,
                    'device'    => $event->device,
                    'logged_at' => now()->toDateTimeString(),
                ],
            ),

            PasswordChanged::class => new NotificationDTO(
                type:     'auth.password_changed',
                subject:  'Your password has been changed',
                template: 'notification::emails.auth.password-changed',
                data: [
                    'name'       => $event->user->name,
                    'ip'         => $event->ip,
                    'changed_at' => now()->toDateTimeString(),
                ],
            ),

            EmailChanged::class => new NotificationDTO(
                type:     'auth.email_changed',
                subject:  'Your email address has been changed',
                template: 'notification::emails.auth.email-changed',
                data: [
                    'name'       => $event->user->name,
                    'old_email'  => $event->oldEmail,
                    'new_email'  => $event->newEmail,
                    'changed_at' => now()->toDateTimeString(),
                ],
            ),

            TwoFactorEnabled::class => new NotificationDTO(
                type:     'auth.2fa_enabled',
                subject:  'Two-factor authentication enabled',
                template: 'notification::emails.auth.two-factor-enabled',
                data: [
                    'name'       => $event->user->name,
                    'ip'         => $event->ip,
                    'enabled_at' => now()->toDateTimeString(),
                ],
            ),

            TwoFactorDisabled::class => new NotificationDTO(
                type:     'auth.2fa_disabled',
                subject:  'Two-factor authentication disabled',
                template: 'notification::emails.auth.two-factor-disabled',
                data: [
                    'name'        => $event->user->name,
                    'ip'          => $event->ip,
                    'disabled_at' => now()->toDateTimeString(),
                ],
            ),

            default => null,
        };
    }
}
