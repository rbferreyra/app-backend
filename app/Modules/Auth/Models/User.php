<?php

namespace App\Modules\Auth\Models;

use App\Shared\Traits\HasPublicUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasPublicUuid;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'password' => 'hashed',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_confirmed_at);
    }

    public function createDeviceToken(string $deviceName, ?Request $request = null): string
    {
        $newToken = $this->createToken($deviceName);

        if ($request) {
            $this->tokens()
                ->where('id', $newToken->accessToken->id)
                ->update([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
        }

        return $newToken->plainTextToken;
    }
}
