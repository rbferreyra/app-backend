<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly Request $request,
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

        if ($user->hasTwoFactorEnabled()) {
            // Token temporário — sem IP/user-agent, será substituído pelo definitivo
            $temporaryToken = $user->createToken('2fa-challenge', ['2fa-challenge'])->plainTextToken;

            return [
                'requires_2fa' => true,
                'temporary_token' => $temporaryToken,
            ];
        }

        // Revoga token anterior do mesmo device
        $user
            ->tokens()
            ->where('name', $deviceName)
            ->delete();

        $token = $user->createDeviceToken($deviceName, $this->request);

        event(new UserLoggedIn($user));

        return [
            'requires_2fa' => false,
            'user' => $user,
            'token' => $token,
        ];
    }
}
