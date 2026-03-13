<?php

namespace App\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    protected $fillable = [
        'method',
        'url',
        'route',
        'status',
        'response_time_ms',
        'ip',
        'user_agent',
        'user_type',
        'user_id',
        'payload',
        'response',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response' => 'array',
        ];
    }
}
