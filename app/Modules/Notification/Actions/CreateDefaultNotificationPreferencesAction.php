<?php

namespace App\Modules\Notification\Actions;

use App\Modules\Notification\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Modules\Notification\Repositories\Contracts\NotificationTypeRepositoryInterface;

class CreateDefaultNotificationPreferencesAction
{
    public function __construct(
        private readonly NotificationTypeRepositoryInterface $typeRepository,
        private readonly NotificationPreferenceRepositoryInterface $preferenceRepository,
    ) {
    }

    public function execute(int $userId): void
    {
        $types = $this->typeRepository->all();

        foreach ($types as $type) {
            $this->preferenceRepository->upsert(
                userId: $userId,
                typeId: $type->id,
                channels: [
                    'email' => true,
                    'whatsapp' => false,
                ],
            );
        }
    }
}
