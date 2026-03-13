<?php

namespace App\Modules\Auth\Listeners;

use App\Modules\Auth\Events\UserLoggedIn;
use App\Shared\Helpers\AuditLogger;

class LogUserLoggedIn
{
    public function handle(UserLoggedIn $event): void
    {
        AuditLogger::log('logged_in', 'auth', $event->user, [
            'ip_address' => $event->ip,
            'user_agent' => $event->userAgent,
            'device' => $event->device,
        ]);
    }
}
