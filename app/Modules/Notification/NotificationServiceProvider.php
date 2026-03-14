<?php

namespace App\Modules\Notification;

use App\Modules\Notification\Channels\Contracts\NotificationChannelInterface;
use App\Modules\Notification\Channels\EmailChannel;
use App\Modules\Notification\Channels\WhatsAppChannel;
use App\Modules\Notification\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Modules\Notification\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Modules\Notification\Repositories\NotificationPreferenceRepository;
use App\Modules\Notification\Repositories\NotificationTypeRepository;
use App\Shared\Providers\ModuleServiceProvider;

class NotificationServiceProvider extends ModuleServiceProvider
{
    protected function routeFilePath(): string
    {
        return __DIR__ . '/Routes/api.php';
    }

    protected function routePrefix(): string
    {
        return 'notifications';
    }

    protected function bindings(): array
    {
        return [
            NotificationTypeRepositoryInterface::class => NotificationTypeRepository::class,
            NotificationPreferenceRepositoryInterface::class => NotificationPreferenceRepository::class,
        ];
    }

    public function boot(): void
    {
        parent::boot();

        $this->loadViewsFrom(
            __DIR__ . '/Resources/views',
            'notification'
        );
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(
            NotificationChannelInterface::class,
            EmailChannel::class,
        );

        $this->app->tag([
            EmailChannel::class,
            WhatsAppChannel::class,
        ], 'notification.channels');
    }
}
