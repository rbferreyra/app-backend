<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListDevicesAction
{
    public function execute(User $user): Collection
    {
        return $user
            ->tokens()
            ->where('name', '!=', '2fa-challenge')
            ->orderByDesc('last_used_at')
            ->get();
    }
}
