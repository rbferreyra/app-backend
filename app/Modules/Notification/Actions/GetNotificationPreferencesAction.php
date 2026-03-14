<?php

namespace App\Modules\Notification\Actions;

use App\Modules\Notification\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Modules\Notification\Repositories\Contracts\NotificationTypeRepositoryInterface;
use Illuminate\Support\Collection;

class GetNotificationPreferencesAction
{
    public function __construct(
        private readonly NotificationTypeRepositoryInterface $typeRepository,
        private readonly NotificationPreferenceRepositoryInterface $preferenceRepository,
    ) {
    }

    public function execute(int $userId): Collection
    {
        $types = $this->typeRepository->all();

        $preferences = $this->preferenceRepository
            ->getByUser($userId)
            ->keyBy('notification_type_id');

        return $types->map(function ($type) use ($preferences) {
            $preference = $preferences->get($type->id);

            return (object) [
                'uuid' => $type->uuid,
                'key' => $type->key,
                'description' => $type->description,
                'channels' => [
                    'email' => $preference?->email ?? true,
                    'whatsapp' => $preference?->whatsapp ?? false,
                ],
            ];
        });
    }
}
