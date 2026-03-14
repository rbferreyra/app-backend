<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\Models\NotificationPreference;
use App\Modules\Notification\Repositories\Contracts\NotificationPreferenceRepositoryInterface;
use App\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class NotificationPreferenceRepository extends BaseRepository implements NotificationPreferenceRepositoryInterface
{
    protected function model(): string
    {
        return NotificationPreference::class;
    }

    public function getByUser(int $userId): Collection
    {
        return $this->model
            ->with('notificationType')
            ->where('user_id', $userId)
            ->get();
    }

    public function getByUserAndType(int $userId, int $typeId): ?NotificationPreference
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('notification_type_id', $typeId)
            ->first();
    }

    public function upsert(int $userId, int $typeId, array $channels): NotificationPreference
    {
        $preference = $this->model->firstOrNew([
            'user_id' => $userId,
            'notification_type_id' => $typeId,
        ]);

        $preference->fill($channels);
        $preference->save();

        return $preference->load('notificationType');
    }
}
