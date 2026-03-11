<?php

namespace App\Modules\Auth\DTOs;

use App\Shared\DTOs\BaseDTO;
use Illuminate\Http\Request;

class UpdateProfileDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $avatar,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            email: $data['email'],
            avatar: $data['avatar'],
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new static(
            name: $request->input('name'),
            email: $request->input('email'),
            avatar: $request->input('avatar'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
        ], fn($value) => ! is_null($value));
    }
}
