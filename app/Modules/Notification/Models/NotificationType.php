<?php

namespace App\Modules\Notification\Models;

use App\Shared\Traits\HasPublicUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationType extends Model
{
    use HasPublicUuid;

    protected $fillable = [
        'key',
        'description',
    ];

    public function preferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }
}
