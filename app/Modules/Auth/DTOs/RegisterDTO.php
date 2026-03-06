<?php

namespace App\Modules\Auth\DTOs;

use App\Shared\DTOs\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone = null,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name:     $data['name'],
            email:    $data['email'],
            password: $data['password'],
            phone:    $data['phone'] ?? null,
        );
    }
}
