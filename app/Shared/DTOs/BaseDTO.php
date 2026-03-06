<?php

namespace App\Shared\DTOs;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    /**
     * Create DTO from plain array.
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Create DTO from a Laravel Request.
     */
    public static function fromRequest(Request $request): static
    {
        return static::fromArray($request->validated());
    }

    /**
     * Serialize DTO to array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Serialize DTO to array, excluding null values.
     */
    public function toArrayWithoutNull(): array
    {
        return array_filter($this->toArray(), fn ($value) => ! is_null($value));
    }
}
