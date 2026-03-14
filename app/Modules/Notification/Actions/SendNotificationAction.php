<?php

namespace App\Modules\Notification\Actions;

use App\Modules\Notification\Channels\Contracts\NotificationChannelInterface;
use App\Modules\Notification\Channels\EmailChannel;
use App\Modules\Notification\Channels\WhatsAppChannel;
use App\Modules\Notification\DTOs\NotificationDTO;
use App\Modules\Notification\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Modules\Notification\Repositories\Contracts\NotificationTypeRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNotificationAction
{
    private array $channels;

    public function __construct(
        private readonly NotificationTypeRepositoryInterface $typeRepository,
        private readonly NotificationPreferenceRepositoryInterface $preferenceRepository,
        private readonly EmailChannel $emailChannel,
        private readonly WhatsAppChannel $whatsAppChannel,
    ) {
        $this->channels = [
            'email' => $this->emailChannel,
            'whatsapp' => $this->whatsAppChannel,
        ];
    }

    public function execute(object $notifiable, NotificationDTO $dto): void
    {
        $type = $this->typeRepository->findByKey($dto->type);

        if (! $type) {
            Log::warning("Notification type not found: {$dto->type}");
            return;
        }

        $preference = $this->preferenceRepository->getByUserAndType(
            $notifiable->id,
            $type->id
        );

        $activeChannels = $preference
            ? $this->resolveFromPreference($preference)
            : ['email'];

        foreach ($activeChannels as $channelName) {
            $channel = $this->channels[$channelName] ?? null;

            if (! $channel) {
                continue;
            }

            $this->dispatch($channel, $notifiable, $dto);
        }
    }

    private function resolveFromPreference(object $preference): array
    {
        $active = [];

        foreach (array_keys($this->channels) as $channelName) {
            if ($preference->{$channelName}) {
                $active[] = $channelName;
            }
        }

        return $active;
    }

    private function dispatch(
        NotificationChannelInterface $channel,
        object $notifiable,
        NotificationDTO $dto
    ): void {
        try {
            $channel->send($notifiable, $dto);
        } catch (Throwable $e) {
            Log::error('Notification dispatch failed', [
                'channel' => get_class($channel),
                'type' => $dto->type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
