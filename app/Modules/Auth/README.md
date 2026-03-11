# Auth Module


## Architecture

```
app/Modules/Auth/
├── Actions/
│   ├── RegisterUserAction.php
│   ├── LoginAction.php
│   ├── LogoutAction.php
│   ├── LogoutAllAction.php
│   ├── SendVerificationEmailAction.php
│   ├── VerifyEmailAction.php
│   ├── ForgotPasswordAction.php
│   ├── ResetPasswordAction.php
│   ├── ChangePasswordAction.php
│   ├── Enable2FAAction.php
│   ├── Confirm2FAAction.php
│   ├── Verify2FAAction.php
│   ├── Disable2FAAction.php
│   ├── RegenerateRecoveryCodesAction.php
│   ├── ListDevicesAction.php
│   ├── RevokeDeviceAction.php
│   ├── RevokeAllDevicesAction.php
│   └── UpdateProfileAction.php
├── Controllers/
│   ├── AuthController.php
│   ├── EmailVerificationController.php
│   ├── PasswordController.php
│   ├── TwoFactorController.php
│   ├── DeviceController.php
│   └── ProfileController.php
├── DTOs/
│   ├── RegisterDTO.php
│   ├── LoginDTO.php
│   └── UpdateProfileDTO.php
├── Events/
│   ├── UserRegistered.php
│   └── UserLoggedIn.php
├── Listeners/
│   └── SendVerificationEmail.php
├── Middleware/
│   └── RequireTwoFactorChallenge.php
├── Models/
│   └── User.php
├── Repositories/
│   ├── Contracts/
│   │   └── UserRepositoryInterface.php
│   └── UserRepository.php
├── Requests/
│   ├── RegisterRequest.php
│   ├── LoginRequest.php
│   ├── UpdateProfileRequest.php
│   ├── Confirm2FARequest.php
│   ├── Verify2FARequest.php
│   └── Disable2FARequest.php
├── Resources/
│   ├── UserResource.php
│   └── DeviceResource.php
└── Routes/
    └── api.php
```

---

## Endpoints

All routes are prefixed with `/api/auth`.  
All responses follow the pattern `{ message, status, data }`.

### 3.1 Core Auth

#### `POST /auth/register`

Creates a new user account and returns an auth token.

**Request**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "device_name": "Chrome - Windows"
}
```

**Response** `201`
```json
{
    "message": "Account created successfully.",
    "status": 201,
    "data": {
        "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
        "token": "1|abc123..."
    }
}
```

---

#### `POST /auth/login`

Authenticates the user. If 2FA is enabled, returns a temporary token instead of the definitive token.

**Request**
```json
{
    "email": "john@example.com",
    "password": "password",
    "device_name": "Chrome - Windows"
}
```

**Response (2FA disabled)** `200`
```json
{
    "message": "Login successful.",
    "status": 200,
    "data": {
        "requires_2fa": false,
        "user": { ... },
        "token": "1|abc123..."
    }
}
```

**Response (2FA enabled)** `200`
```json
{
    "message": "Two-factor authentication required.",
    "status": 200,
    "data": {
        "requires_2fa": true,
        "temporary_token": "2|xyz789..."
    }
}
```

> The `temporary_token` has the `2fa-challenge` ability and can only be used on `POST /auth/2fa/verify`.

---

#### `POST /auth/logout` 🔒

Revokes the current token.

**Response** `200`
```json
{ "message": "Logged out successfully.", "status": 200 }
```

---

#### `POST /auth/logout/all` 🔒

Revokes all tokens of the authenticated user.

**Response** `200`
```json
{ "message": "Logged out from all devices successfully.", "status": 200 }
```

---

### 3.2 Email Verification

#### `GET /auth/email/verify/{id}/{hash}`

Verifies the user's email address via signed URL sent by email.

**Response** `200`
```json
{ "message": "Email verified successfully.", "status": 200 }
```

---

#### `POST /auth/email/resend` 🔒

Resends the verification email.

**Response** `200`
```json
{ "message": "Verification email resent.", "status": 200 }
```

---

### 3.3 Password Management

#### `POST /auth/password/forgot`

Sends a password reset link to the given email.

**Request**
```json
{ "email": "john@example.com" }
```

**Response** `200`
```json
{ "message": "Password reset link sent.", "status": 200 }
```

---

#### `POST /auth/password/reset`

Resets the user's password using the token from the reset email.

**Request**
```json
{
    "token": "reset-token",
    "email": "john@example.com",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

**Response** `200`
```json
{ "message": "Password reset successfully.", "status": 200 }
```

---

#### `PUT /auth/password/change` 🔒

Changes the password for the authenticated user.

**Request**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

**Response** `200`
```json
{ "message": "Password changed successfully.", "status": 200 }
```

---

### 3.4 Two-Factor Authentication (TOTP)

Packages: `pragmarx/google2fa-laravel` + `bacon/bacon-qr-code`

#### Flow

```
1. POST /auth/2fa/enable    → generates secret + QR code
2. POST /auth/2fa/confirm   → validates first TOTP code, activates 2FA, returns recovery codes
3. POST /auth/login         → returns temporary_token when 2FA is active
4. POST /auth/2fa/verify    → validates TOTP code, returns definitive token
5. POST /auth/2fa/disable   → deactivates 2FA (requires password)
```

---

#### `POST /auth/2fa/enable` 🔒

Generates a TOTP secret and returns the QR code. Does **not** activate 2FA yet.

**Response** `200`
```json
{
    "message": "QR code generated. Confirm with /2fa/confirm.",
    "status": 200,
    "data": {
        "secret": "JBSWY3DPEHPK3PXP",
        "qr_code": "data:image/svg+xml;base64,..."
    }
}
```

---

#### `POST /auth/2fa/confirm` 🔒

Confirms 2FA with the first TOTP code from the authenticator app. Activates 2FA and returns recovery codes.

**Request**
```json
{ "code": "123456" }
```

**Response** `200`
```json
{
    "message": "2FA enabled successfully.",
    "status": 200,
    "data": {
        "recovery_codes": [
            "ABCDE-FGHIJ",
            "KLMNO-PQRST",
            "..."
        ]
    }
}
```

---

#### `POST /auth/2fa/verify` 🔒 `[2fa-challenge]`

> Requires the `temporary_token` from login as Bearer token.  
> Protected by `RequireTwoFactorChallenge` middleware.

Validates the TOTP code (or a recovery code), revokes the temporary token, and returns the definitive token.

**Request**
```json
{
    "code": "123456",
    "device_name": "Chrome - Windows"
}
```

**Response** `200`
```json
{
    "message": "2FA verified successfully.",
    "status": 200,
    "data": {
        "user": { ... },
        "token": "3|definitivetoken..."
    }
}
```

> Recovery codes are one-time use. Each used code is permanently removed.

---

#### `POST /auth/2fa/disable` 🔒

Deactivates 2FA. Requires the current password for confirmation.

**Request**
```json
{ "password": "current-password" }
```

**Response** `200`
```json
{ "message": "2FA disabled successfully.", "status": 200 }
```

---

#### `GET /auth/2fa/recovery-codes` 🔒

Returns the current recovery codes.

**Response** `200`
```json
{
    "message": "Recovery codes retrieved successfully.",
    "status": 200,
    "data": {
        "recovery_codes": ["ABCDE-FGHIJ", "..."]
    }
}
```

---

#### `POST /auth/2fa/recovery-codes/regenerate` 🔒

Generates a new set of 8 recovery codes, invalidating all previous ones.

**Response** `200`
```json
{
    "message": "Recovery codes regenerated successfully.",
    "status": 200,
    "data": {
        "recovery_codes": ["VWXYZ-12345", "..."]
    }
}
```

---

### 3.5 Device Management

Devices are the Sanctum `personal_access_tokens` enriched with `ip_address` and `user_agent`.  
Token creation always saves IP and user-agent via `User::createDeviceToken()`.  
On each login, the previous token for the same `device_name` is revoked to prevent accumulation.

#### `GET /auth/devices` 🔒

Lists all active devices (excludes `2fa-challenge` tokens), ordered by last use.

**Response** `200`
```json
{
    "message": "Devices retrieved successfully.",
    "status": 200,
    "data": [
        {
            "id": 1,
            "name": "Chrome - Windows",
            "ip_address": "192.168.1.1",
            "user_agent": "Mozilla/5.0 ...",
            "last_used_at": "2025-01-15T10:30:00+00:00",
            "created_at": "2025-01-10T08:00:00+00:00",
            "is_current": true
        }
    ]
}
```

---

#### `DELETE /auth/devices/{id}` 🔒

Revokes a specific device token by ID.

**Response** `200`
```json
{ "message": "Device revoked successfully.", "status": 200 }
```

---

#### `DELETE /auth/devices` 🔒

Revokes all devices except the current one.

**Response** `200`
```json
{ "message": "All other devices revoked successfully.", "status": 200 }
```

---

### 3.6 Profile

#### `GET /auth/me` 🔒

Returns the authenticated user's profile.

**Response** `200`
```json
{
    "message": "Profile retrieved successfully.",
    "status": 200,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar": "https://cdn.example.com/avatar.jpg",
        "email_verified_at": "2025-01-10T08:00:00+00:00"
    }
}
```

---

#### `PUT /auth/profile` 🔒

Updates the authenticated user's profile. All fields are optional (partial update).  
If the email is changed, `email_verified_at` is reset to `null` and a new verification email is sent.

**Request**
```json
{
    "name": "New Name",
    "email": "newemail@example.com",
    "avatar": "https://cdn.example.com/new-avatar.jpg"
}
```

**Response** `200`
```json
{
    "message": "Profile updated successfully.",
    "status": 200,
    "data": {
        "id": 1,
        "name": "New Name",
        "email": "newemail@example.com",
        "avatar": "https://cdn.example.com/new-avatar.jpg",
        "email_verified_at": null
    }
}
```

---

## Conventions

### Response format

All responses follow the `ApiResponseTrait` pattern:

```json
{
    "message": "Human-readable message in English.",
    "status": 200,
    "data": { }
}
```

### Authentication

Protected routes 🔒 require `Authorization: Bearer {token}` header.  
Routes marked `[2fa-challenge]` only accept tokens with the `2fa-challenge` ability.

### Token lifecycle

| Event | Action |
|---|---|
| Register | Creates token with `ip_address` + `user_agent` |
| Login (no 2FA) | Revokes previous token for same `device_name`, creates new token |
| Login (2FA active) | Creates temporary `2fa-challenge` token |
| 2FA verify | Revokes temporary token + previous token for same `device_name`, creates definitive token |
| Logout | Revokes current token |
| Logout all | Revokes all tokens |
| Revoke device | Revokes specific token by ID |

### 2FA secret storage

The TOTP secret is stored encrypted via Laravel's `encrypt()` / `decrypt()` helpers.  
Recovery codes are stored as a JSON array, consumed one-time on use.

### Database — `personal_access_tokens`

Custom columns added via migration:

| Column | Type | Description |
|---|---|---|
| `ip_address` | `varchar(45)` | IPv4 or IPv6 of the request |
| `user_agent` | `varchar` | Raw user-agent string |

### Database — `users`

2FA columns:

| Column | Type | Description |
|---|---|---|
| `two_factor_secret` | `string` | Encrypted TOTP secret |
| `two_factor_confirmed_at` | `datetime` | When 2FA was activated (`null` = inactive) |
| `two_factor_recovery_codes` | `json` | Array of one-time recovery codes |
