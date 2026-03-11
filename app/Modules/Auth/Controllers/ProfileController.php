<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\UpdateProfileAction;
use App\Modules\Auth\DTOs\UpdateProfileDTO;
use App\Modules\Auth\Requests\UpdateProfileRequest;
use App\Modules\Auth\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'Profile retrieved successfully.'
        );
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $dto  = UpdateProfileDTO::fromRequest($request);
        $user = $action->execute($request->user(), $dto);

        return $this->success(
            new UserResource($user),
            'Profile updated successfully.'
        );
    }
}
