<?php

namespace App\Modules\Auth\Repositories\Contracts;

use App\Modules\Auth\Models\User;
use App\Shared\Contracts\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function existsByEmail(string $email): bool;
}
