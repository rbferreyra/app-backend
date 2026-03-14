<?php

namespace App\Modules\Notification\Channels;

use App\Modules\Notification\Channels\Contracts\NotificationChannelInterface;
use App\Modules\Notification\DTOs\NotificationDTO;
use App\Modules\Notification\Mail\GenericMail;
use Illuminate\Support\Facades\Mail;

class EmailChannel implements NotificationChannelInterface
{
    public function send(object $notifiable, NotificationDTO $dto): void
    {
        if (empty($notifiable->email)) {
            return;
        }

        Mail::to($notifiable->email)
            ->queue(new GenericMail($dto));
    }
}
