<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Disable2FARequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }
}
