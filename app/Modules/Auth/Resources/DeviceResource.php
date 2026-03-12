<?php

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'last_used_at' => $this->last_used_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'is_current' => $this->id === $request->user()->currentAccessToken()->id,
        ];
    }
}
