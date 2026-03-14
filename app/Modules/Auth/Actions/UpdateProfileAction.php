<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\UpdateProfileDTO;
use App\Modules\Auth\Events\EmailChanged;
use App\Modules\Auth\Models\User;

class UpdateProfileAction
{
    public function execute(User $user, UpdateProfileDTO $dto): User
    {
        $data = $dto->toArray();

        if (empty($data)) {
            return $user;
        }

        // Se e-mail mudou, força nova verificação
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $data['email_verified_at'] = null;

            event(new EmailChanged(
                user: $user,
                oldEmail: $user->email,
                newEmail: $data['email'],
            ));
        }

        $user->update($data);

        return $user->fresh();
    }
}
