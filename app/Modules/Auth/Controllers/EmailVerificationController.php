<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\ResendVerificationAction;
use App\Modules\Auth\Actions\VerifyEmailAction;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly VerifyEmailAction $verifyEmailAction,
        private readonly ResendVerificationAction $resendVerificationAction,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return $this->notFound('User not found.');
        }

        try {
            $this->verifyEmailAction->execute($user, $hash);
        } catch (AuthorizationException $e) {
            return $this->error($e->getMessage(), 403);
        }

        return $this->success(null, 'Email verified successfully.');
    }

    public function resend(Request $request): JsonResponse
    {
        $this->resendVerificationAction->execute($request->user());

        return $this->success(null, 'Verification email sent.');
    }
}
