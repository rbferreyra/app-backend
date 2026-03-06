<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\RegisterUserAction;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterUserAction $registerAction,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto  = RegisterDTO::fromRequest($request);
        $user = $this->registerAction->execute($dto);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Account created successfully.');
    }
}
