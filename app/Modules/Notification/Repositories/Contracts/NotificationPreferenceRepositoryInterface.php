<?php

namespace App\Modules\Notification\Repositories\Contracts;

use App\Shared\Contracts\RepositoryInterface;
use App\Modules\Notification\Models\NotificationPreference;
use Illuminate\Database\Eloquent\Collection;

interface NotificationPreferenceRepositoryInterface extends RepositoryInterface
{
    public function getByUser(int $userId): Collection;
    public function getByUserAndType(int $userId, int $typeId): ?NotificationPreference;
    public function upsert(int $userId, int $typeId, array $channels): NotificationPreference;
}
