<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\ChangePasswordAction;
use App\Modules\Auth\Actions\ForgotPasswordAction;
use App\Modules\Auth\Actions\ResetPasswordAction;
use App\Modules\Auth\DTOs\ChangePasswordDTO;
use App\Modules\Auth\DTOs\ResetPasswordDTO;
use App\Modules\Auth\Requests\ChangePasswordRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    public function __construct(
        private readonly ForgotPasswordAction $forgotPasswordAction,
        private readonly ResetPasswordAction $resetPasswordAction,
        private readonly ChangePasswordAction $changePasswordAction,
    ) {
    }

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $this->forgotPasswordAction->execute($request->email);

        return $this->success(null, 'If this email exists, a reset link has been sent.');
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $dto = ResetPasswordDTO::fromRequest($request);
        $this->resetPasswordAction->execute($dto);

        return $this->success(null, 'Password reset successfully.');
    }

    public function change(ChangePasswordRequest $request): JsonResponse
    {
        $dto = ChangePasswordDTO::fromRequest($request);
        $this->changePasswordAction->execute($request->user(), $dto);

        return $this->success(null, 'Password changed successfully.');
    }
}
