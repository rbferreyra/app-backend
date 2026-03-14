<?php

namespace App\Modules\Notification\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'preferences' => ['required', 'array', 'min:1'],
            'preferences.*.notification_type_uuid' => ['required', 'uuid'],
            'preferences.*.channels' => ['required', 'array'],
            'preferences.*.channels.email' => ['sometimes', 'boolean'],
            'preferences.*.channels.whatsapp' => ['sometimes', 'boolean'],
        ];
    }
}
