# app-backend — ROADMAP

> Laravel 12 REST API — Real Estate Platform  
> Modular architecture: `app/Modules/{Module}` + `app/Shared`  
> Conventions: Actions · Repositories · DTOs · Resources · Events/Listeners  
> All API response messages in English. Response pattern: `{ message, status, data }`

---

## ✅ Module 3 — Auth (completed)

### 3.1 Core Auth
- `POST /api/auth/register`
- `POST /api/auth/login` — supports 2FA flow (returns `temporary_token` when 2FA active)
- `POST /api/auth/logout`
- `POST /api/auth/logout/all`

### 3.2 Email Verification
- `GET /api/auth/email/verify/{id}/{hash}`
- `POST /api/auth/email/resend`
- Queued listener `SendVerificationEmail` on `UserRegistered` event

### 3.3 Password Management
- `POST /api/auth/password/forgot`
- `POST /api/auth/password/reset`
- `PUT /api/auth/password/change`

### 3.4 Two-Factor Authentication (TOTP)
- `POST /api/auth/2fa/enable` — generates secret + QR code (SVG base64)
- `POST /api/auth/2fa/confirm` — activates 2FA, returns 8 recovery codes
- `POST /api/auth/2fa/verify` — validates TOTP/recovery code, returns definitive token
- `POST /api/auth/2fa/disable` — requires password
- `GET /api/auth/2fa/recovery-codes`
- `POST /api/auth/2fa/recovery-codes/regenerate`
- Packages: `pragmarx/google2fa-laravel` + `bacon/bacon-qr-code`
- Secret stored encrypted via Laravel `encrypt()`/`decrypt()`
- Login flow: temporary token with `2fa-challenge` ability → verify → definitive token
- Middleware: `RequireTwoFactorChallenge`

### 3.5 Device Management
- `GET /api/auth/devices` — lists tokens (excludes 2fa-challenge), ordered by last use
- `DELETE /api/auth/devices/{uuid}` — revoke specific device
- `DELETE /api/auth/devices` — revoke all except current
- `personal_access_tokens` extended with `ip_address` + `user_agent`
- `User::createDeviceToken()` saves IP + user-agent on every token creation
- Login revokes previous token for same `device_name` before creating new one
- Custom model `PersonalAccessToken extends SanctumPersonalAccessToken` registered via `Sanctum::usePersonalAccessTokenModel()`

### 3.6 Profile
- `GET /api/auth/me`
- `PUT /api/auth/profile` — name, email, avatar (partial update)
- Email change resets `email_verified_at` to null

### 3.7 README
- File: `AUTH_MODULE.md`

### Improvements applied post-3.7
- UUID added to all entities exposed by API
- Trait `HasPublicUuid` in `app/Shared/Traits/` — keeps internal `id`, exposes `uuid` externally
- `users` and `personal_access_tokens` have `uuid` column (unique)
- All Resources expose `uuid` instead of `id`
- Routes use `{uuid}` parameter, Actions query by `uuid`

---

## 🔜 Module 4 — Cross-cutting Improvements

### 4.1 — Audit Log
- Package: `spatie/laravel-activitylog`
- Trait `LogsActivity` on models that need auditing
- Log: who changed what, before/after values

### 4.2 — API Request Log
- Custom middleware `LogApiRequest`
- Table: `api_logs` (method, url, ip, user_agent, user_id, status, response_time, payload)
- Mask sensitive fields (`password`, `token`, etc.)
- Configurable: enable/disable per environment, exclude routes

### 4.3 — Notification Module
- `app/Modules/Notification/`
- Centralize all Mailables here
- Publish and customize Laravel mail templates (`php artisan vendor:publish --tag=laravel-mail`)
- Support multiple layouts: transactional, alert, marketing
- All mail queued by default (`ShouldQueue`)

### 4.4 — New Auth Events
- `UserLoggedOut` → audit log
- `PasswordChanged` → confirmation email
- `EmailChanged` → warning email to old address
- `TwoFactorEnabled` → security confirmation email
- `TwoFactorDisabled` → security alert email
- `DeviceRevoked` → audit log
- `LoginFailed` → audit log + feed into rate limiting (4.6)

### 4.5 — Roles & Permissions
- Package: `spatie/laravel-permission`
- Roles: `super-admin`, `admin`, `agent`, `assistant`, `client`
- Permissions per domain: `property:create`, `property:publish`, `user:manage`, `report:view`, etc.

### 4.6 — Advanced Rate Limiting
- Block by IP + by email after N failed login attempts
- Redis keys: `login_attempts:ip:{ip}`, `login_attempts:email:{email}`
- Auto-unblock after configurable TTL
- Integrates with `LoginFailed` event (4.4)

---

## 🔜 Module 4.7 — Tenant (Multi-tenancy)

Single database, tenant-based isolation.

- Model `Tenant` — central entity of the platform
- `users.tenant_id` FK
- Shared trait `BelongsToTenant` (in `app/Shared/Traits/`)
  - Global scope: auto-filters queries by `auth()->user()->tenant_id`
  - Auto-fills `tenant_id` on `creating`
- Middleware `ResolveTenant`
- Structure:
```
app/Modules/Tenant/
├── Actions/
├── Controllers/
├── DTOs/
├── Models/Tenant.php
├── Middleware/ResolveTenant.php
├── Repositories/
├── Resources/TenantResource.php
└── Routes/api.php

app/Shared/Traits/BelongsToTenant.php
```

---

## 🔜 Module 4.8 — Plans

- Model `Plan` with features as JSON:
```json
{
    "max_properties": 50,
    "max_photos_per_property": 20,
    "max_users": 3,
    "max_portals": 3,
    "has_reports": true,
    "has_api_access": false
}
```
- Plans: `free`, `pro`, `business`
- `Tenant` has current `plan_id`
- Service `PlanLimitService` — checks limits before creating resources

---

## 🔜 Module 4.9 — Billing & Payments

- Gateway: **Asaas** (PIX + Boleto + Card + native subscriptions)
- Driver-based pattern — swap gateway without rewriting billing logic:
```php
// config/billing.php
'driver' => env('BILLING_DRIVER', 'asaas'),
```
- `Subscription` model — status: `trialing`, `active`, `past_due`, `canceled`
- `Invoice` model — history of charges
- Webhook handler for Asaas events
- Trial period support
- Upgrade/downgrade plan flow

---

## 🔜 Module 5 — Properties (Imóveis)

Depends on: Tenant (4.7) · Plans (4.8) · Roles (4.5) · Audit (4.1)

- Full CRUD for real estate listings
- Plan limit enforcement (`max_properties`)
- Permission gates (`property:create`, `property:publish`, etc.)
- Events: `PropertyCreated`, `PropertyPublished`, `PropertyArchived`
- Photo management (with `max_photos_per_property` limit from plan)
- Portal distribution (HUB system — based on redeurbana experience)

---

## Architecture conventions

| Layer | Convention |
|---|---|
| Actions | Single responsibility, `execute()` method |
| Repositories | `BaseRepository` + `RepositoryInterface`, abstracts Eloquent |
| DTOs | `BaseDTO` with `fromArray`, `fromRequest`, `toArray` |
| Resources | Format JSON responses |
| Requests | All validation here |
| Events/Listeners | Side-effects only |
| Services | External API integrations only |

## Shared utilities (`app/Shared/`)

| File | Purpose |
|---|---|
| `Traits/ApiResponseTrait.php` | `success()`, `error()`, `created()`, `noContent()` |
| `Traits/HasPublicUuid.php` | Adds `uuid` column, exposes via `getRouteKeyName()` |
| `Traits/BelongsToTenant.php` | Global scope + auto-fill `tenant_id` (4.7) |
| `DTOs/BaseDTO.php` | Base DTO with `fromArray`, `fromRequest`, `toArray` |
| `Repositories/BaseRepository.php` | Base repository implementation |
| `Contracts/RepositoryInterface.php` | Repository contract |
| `Exceptions/ModelNotFoundException.php` | Custom 404 exception |
| `Providers/ModuleServiceProvider.php` | Base provider for module route/binding registration |
