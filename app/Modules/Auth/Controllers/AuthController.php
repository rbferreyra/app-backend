<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\LoginAction;
use App\Modules\Auth\Actions\LogoutAction;
use App\Modules\Auth\Actions\LogoutAllAction;
use App\Modules\Auth\Actions\RegisterUserAction;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterUserAction $registerAction,
        private readonly LoginAction $loginAction,
        private readonly LogoutAction $logoutAction,
        private readonly LogoutAllAction $logoutAllAction,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDTO::fromRequest($request);
        $user = $this->registerAction->execute($dto);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Account created successfully.');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request);
        $result = $this->loginAction->execute($dto);

        if ($result['requires_2fa']) {
            return $this->success([
                'requires_2fa' => true,
                'temporary_token' => $result['temporary_token'],
            ], 'Two-factor authentication required.');
        }

        return $this->success([
            'requires_2fa' => false,
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutAction->execute($request->user());

        return $this->noContent('Logged out successfully.');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $this->logoutAllAction->execute($request->user());

        return $this->noContent('Logged out from all devices successfully.');
    }
}
