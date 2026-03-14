<?php

namespace App\Modules\Notification\Channels;

use App\Modules\Notification\Channels\Contracts\NotificationChannelInterface;
use App\Modules\Notification\DTOs\NotificationDTO;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel implements NotificationChannelInterface
{
    public function send(object $notifiable, NotificationDTO $dto): void
    {
        // Stub — integration pending (Meta Cloud API / Twilio)
        Log::channel('notifications')->info('WhatsApp notification stub', [
            'to' => $notifiable->phone ?? 'unknown',
            'type' => $dto->type,
            'subject' => $dto->subject,
        ]);
    }
}
