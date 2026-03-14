<?php

namespace App\Modules\Notification\Channels\Contracts;

use App\Modules\Notification\DTOs\NotificationDTO;

interface NotificationChannelInterface
{
    public function send(object $notifiable, NotificationDTO $dto): void;
}
