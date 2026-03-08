<?php

namespace App\Modules\Auth\DTOs;

use App\Shared\DTOs\BaseDTO;

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
}
