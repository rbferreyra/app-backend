<?php

namespace App\Modules\Auth\Models;

use App\Shared\Traits\HasPublicUuid;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasPublicUuid;
}
