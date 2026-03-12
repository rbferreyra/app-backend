<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\ListDevicesAction;
use App\Modules\Auth\Actions\RevokeAllDevicesAction;
use App\Modules\Auth\Actions\RevokeDeviceAction;
use App\Modules\Auth\Resources\DeviceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request, ListDevicesAction $action): JsonResponse
    {
        $devices = $action->execute($request->user());

        return $this->success(
            DeviceResource::collection($devices),
            'Devices retrieved successfully.'
        );
    }

    public function destroy(Request $request, string $uuid, RevokeDeviceAction $action): JsonResponse
    {
        $action->execute($request->user(), $uuid);

        return $this->noContent('Device revoked successfully.');
    }

    public function destroyAll(Request $request, RevokeAllDevicesAction $action): JsonResponse
    {
        $action->execute($request->user());

        return $this->noContent('All other devices revoked successfully.');
    }
}
