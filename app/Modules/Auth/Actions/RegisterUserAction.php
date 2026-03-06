<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RegisterUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(RegisterDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->create($dto->toArray());

            event(new UserRegistered($user));

            return $user;
        });
    }
}
