<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\Confirm2FAAction;
use App\Modules\Auth\Actions\Disable2FAAction;
use App\Modules\Auth\Actions\Enable2FAAction;
use App\Modules\Auth\Actions\RegenerateRecoveryCodesAction;
use App\Modules\Auth\Actions\Verify2FAAction;
use App\Modules\Auth\Requests\Confirm2FARequest;
use App\Modules\Auth\Requests\Disable2FARequest;
use App\Modules\Auth\Requests\Verify2FARequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function enable(Request $request, Enable2FAAction $action): JsonResponse
    {
        $data = $action->execute($request->user());

        return $this->success($data, 'QR code generated. Confirm with /2fa/confirm.');
    }

    public function confirm(Confirm2FARequest $request, Confirm2FAAction $action): JsonResponse
    {
        $data = $action->execute($request->user(), $request->input('code'));

        return $this->success($data, '2FA enabled successfully.');
    }

    public function verify(Verify2FARequest $request, Verify2FAAction $action): JsonResponse
    {
        $data = $action->execute(
            $request->user(),
            $request->input('code'),
            $request->input('device_name', 'auth_token')
        );

        return $this->success($data, '2FA verified successfully.');
    }

    public function disable(Disable2FARequest $request, Disable2FAAction $action): JsonResponse
    {
        $action->execute($request->user(), $request->input('password'));

        return $this->success(null, '2FA disabled successfully.');
    }

    public function recoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return $this->error('2FA is not enabled.', 422);
        }

        return $this->success(
            ['recovery_codes' => $user->two_factor_recovery_codes],
            'Recovery codes retrieved successfully.'
        );
    }

    public function regenerateRecoveryCodes(
        Request $request,
        RegenerateRecoveryCodesAction $action
    ): JsonResponse {
        $codes = $action->execute($request->user());

        return $this->success(
            ['recovery_codes' => $codes],
            'Recovery codes regenerated successfully.'
        );
    }
}
