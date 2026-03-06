<?php

namespace App\Shared\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage() ?: 'Resource not found.',
            'status'  => 404,
        ], 404);
    }
}
