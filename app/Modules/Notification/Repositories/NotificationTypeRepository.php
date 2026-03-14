<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\Models\NotificationType;
use App\Modules\Notification\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class NotificationTypeRepository extends BaseRepository implements NotificationTypeRepositoryInterface
{
    protected function model(): string
    {
        return NotificationType::class;
    }

    public function findByKey(string $key): mixed
    {
        return $this->model->where('key', $key)->first();
    }

    public function findByUuid(string $uuid): mixed
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function all(): Collection
    {
        return $this->model->orderBy('key')->get();
    }
}
