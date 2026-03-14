<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Actions\GetNotificationPreferencesAction;
use App\Modules\Notification\Actions\UpdateNotificationPreferencesAction;
use App\Modules\Notification\Requests\UpdateNotificationPreferencesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function index(
        Request $request,
        GetNotificationPreferencesAction $action
    ): JsonResponse {
        $preferences = $action->execute($request->user()->id);

        return $this->success($preferences, 'Notification preferences retrieved successfully.');
    }

    public function update(
        UpdateNotificationPreferencesRequest $request,
        UpdateNotificationPreferencesAction $action
    ): JsonResponse {
        $preferences = $action->execute(
            $request->user()->id,
            $request->input('preferences')
        );

        return $this->success($preferences, 'Notification preferences updated successfully.');
    }
}
