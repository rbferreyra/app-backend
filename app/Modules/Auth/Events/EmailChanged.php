<?php

namespace App\Modules\Auth\Events;

use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $oldEmail,
        public readonly string $newEmail,
    ) {
    }
}
