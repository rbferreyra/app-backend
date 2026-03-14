<?php

namespace App\Modules\Notification\Models;

use App\Shared\Traits\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Auth\Models\User;

class NotificationPreference extends Model
{
    use HasPublicUuid;

    protected $fillable = [
        'user_id',
        'notification_type_id',
        'email',
        'whatsapp',
    ];

    protected function casts(): array
    {
        return [
            'email' => 'boolean',
            'whatsapp' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notificationType(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class);
    }
}
