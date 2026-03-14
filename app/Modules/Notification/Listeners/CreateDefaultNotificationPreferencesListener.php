<?php

namespace App\Modules\Notification\Listeners;

use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Notification\Actions\CreateDefaultNotificationPreferencesAction;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateDefaultNotificationPreferencesListener implements ShouldQueue
{
    public function __construct(
        private readonly CreateDefaultNotificationPreferencesAction $action,
    ) {
    }

    public function handle(UserRegistered $event): void
    {
        $this->action->execute($event->user->id);
    }
}
