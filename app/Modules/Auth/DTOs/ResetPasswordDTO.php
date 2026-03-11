<?php

namespace App\Modules\Auth\DTOs;

use App\Shared\DTOs\BaseDTO;

class ResetPasswordDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $token,
        public readonly string $password,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            email: $data['email'],
            token: $data['token'],
            password: $data['password'],
        );
    }
}
