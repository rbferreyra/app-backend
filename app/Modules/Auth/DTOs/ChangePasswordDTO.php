<?php

namespace App\Modules\Auth\DTOs;

use App\Shared\DTOs\BaseDTO;

class ChangePasswordDTO extends BaseDTO
{
    public function __construct(
        public readonly string $current_password,
        public readonly string $password,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            current_password: $data['current_password'],
            password: $data['password'],
        );
    }
}
