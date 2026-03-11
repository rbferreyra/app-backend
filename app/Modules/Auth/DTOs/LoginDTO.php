<?php

namespace App\Modules\Auth\DTOs;

use App\Shared\DTOs\BaseDTO;
use Illuminate\Http\Request;

class LoginDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $device_name = null,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            email: $data['email'],
            password: $data['password'],
            device_name: $data['device_name'] ?? null,
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new static(
            email: $request->input('email'),
            password: $request->input('password'),
            device_name: $request->input('device_name'),
        );
    }
}
