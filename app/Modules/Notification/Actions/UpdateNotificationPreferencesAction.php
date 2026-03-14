<?php

namespace App\Modules\Notification\Actions;

use App\Modules\Notification\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Modules\Notification\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Shared\Exceptions\ModelNotFoundException;
use Illuminate\Support\Collection;

class UpdateNotificationPreferencesAction
{
    public function __construct(
        private readonly NotificationTypeRepositoryInterface $typeRepository,
        private readonly NotificationPreferenceRepositoryInterface $preferenceRepository,
    ) {
    }

    public function execute(int $userId, array $preferences): Collection
    {
        $updated = collect();

        foreach ($preferences as $item) {
            $type = $this->typeRepository->findByUuid($item['notification_type_uuid']);

            if (! $type) {
                throw new ModelNotFoundException('Notification type not found.');
            }

            $preference = $this->preferenceRepository->upsert(
                userId: $userId,
                typeId: $type->id,
                channels: $item['channels'],
            );

            $updated->push((object) [
                'uuid' => $type->uuid,
                'key' => $type->key,
                'description' => $type->description,
                'channels' => [
                    'email' => $preference->email,
                    'whatsapp' => $preference->whatsapp,
                ],
            ]);
        }

        return $updated;
    }
}
