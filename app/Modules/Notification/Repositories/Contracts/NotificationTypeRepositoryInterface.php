<?php

namespace App\Modules\Notification\Repositories\Contracts;

use App\Shared\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface NotificationTypeRepositoryInterface extends RepositoryInterface
{
    public function findByKey(string $key): mixed;
    public function findByUuid(string $uuid): mixed;
    public function all(): Collection;
}
