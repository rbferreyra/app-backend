<?php

namespace App\Modules\Auth\Listeners;

use App\Modules\Auth\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationEmail implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        $event->user->sendEmailVerificationNotification();
    }
}
