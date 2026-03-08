<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function execute(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $deviceName = $dto->device_name ?? 'auth_token';

        $token = $user->createToken($deviceName)->plainTextToken;

        event(new UserLoggedIn($user));

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}
