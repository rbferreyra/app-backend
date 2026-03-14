# Notification Module

Laravel 12 REST API — `app/Modules/Notification`

---

## Overview

Centralized multi-channel notification system. Supports email, WhatsApp, and any future channel through a unified interface. Users can configure which notifications they want to receive and through which channels via the preferences system.

---

## Architecture

```
app/Modules/Notification/
├── Actions/
│   ├── SendNotificationAction.php
│   ├── GetNotificationPreferencesAction.php
│   └── UpdateNotificationPreferencesAction.php
├── Channels/
│   ├── Contracts/
│   │   └── NotificationChannelInterface.php
│   ├── EmailChannel.php
│   └── WhatsAppChannel.php
├── Controllers/
│   └── NotificationPreferenceController.php
├── DTOs/
│   └── NotificationDTO.php
├── Listeners/
│   └── SendSystemNotification.php
├── Mail/
│   └── GenericMail.php
├── Models/
│   ├── NotificationType.php
│   └── NotificationPreference.php
├── Repositories/
│   ├── Contracts/
│   │   ├── NotificationTypeRepositoryInterface.php
│   │   └── NotificationPreferenceRepositoryInterface.php
│   ├── NotificationTypeRepository.php
│   └── NotificationPreferenceRepository.php
├── Requests/
│   └── UpdateNotificationPreferencesRequest.php
├── Resources/
│   ├── NotificationTypeResource.php
│   ├── NotificationPreferenceResource.php
│   └── views/
│       └── emails/
│           ├── layouts/
│           │   ├── base.blade.php
│           │   └── alert.blade.php
│           └── auth/
│               ├── verify-email.blade.php
│               ├── reset-password.blade.php
│               ├── password-changed.blade.php
│               ├── email-changed.blade.php
│               ├── two-factor-enabled.blade.php
│               ├── two-factor-disabled.blade.php
│               └── login-detected.blade.php
├── Providers/
│   └── NotificationServiceProvider.php
└── Routes/
    └── api.php
```

---

## Notification Types

Each system event maps to a notification type. Types are seeded into the `notification_types` table via `NotificationTypeSeeder`.

| key | description | layout |
|---|---|---|
| `auth.login` | New login detected | alert |
| `auth.password_changed` | Password changed | base |
| `auth.email_changed` | Email address changed | alert |
| `auth.2fa_enabled` | Two-factor authentication enabled | base |
| `auth.2fa_disabled` | Two-factor authentication disabled | alert |
| `auth.device_revoked` | A device was revoked | alert |
| `property.published` | Property published | base |
| `property.archived` | Property archived | base |
| `billing.invoice_due` | Invoice due | alert |
| `billing.payment_confirmed` | Payment confirmed | base |
| `billing.subscription_canceled` | Subscription canceled | alert |

---

## Channels

All channels implement `NotificationChannelInterface`:

```php
interface NotificationChannelInterface
{
    public function send(object $notifiable, NotificationDTO $dto): void;
}
```

| Channel | Status | Integration |
|---|---|---|
| `EmailChannel` | ✅ Implemented | Laravel Mail + Blade templates |
| `WhatsAppChannel` | 🔜 Stub | Meta Cloud API / Twilio |

> Stub channels log to `storage/logs/notifications.log` until real integration is implemented.

---

## Notification Flow

```
System Event dispatched (e.g. UserLoggedIn)
    → Listener: SendSystemNotification (queued)
        → Resolves $notifiable from $event->user ?? $event->notifiable
        → Resolves NotificationDTO via match(get_class($event))
        → SendNotificationAction::execute($notifiable, $dto)
            → Finds NotificationType by $dto->type
            → Fetches user preferences (defaults: email=true, whatsapp=false)
            → For each active channel:
                → EmailChannel::send()   or
                → WhatsAppChannel::send()
```

---

## Auth Events covered

| Event | Notification Type | Triggered by |
|---|---|---|
| `UserLoggedIn` | `auth.login` | `LoginAction` + `Verify2FAAction` |
| `PasswordChanged` | `auth.password_changed` | `ChangePasswordAction` + `ResetPasswordAction` |
| `EmailChanged` | `auth.email_changed` | `UpdateProfileAction` |
| `TwoFactorEnabled` | `auth.2fa_enabled` | `Confirm2FAAction` |
| `TwoFactorDisabled` | `auth.2fa_disabled` | `Disable2FAAction` |

### Auth Events registered in `AuthServiceProvider`

```php
Event::listen(UserLoggedIn::class,      SendSystemNotification::class);
Event::listen(PasswordChanged::class,   SendSystemNotification::class);
Event::listen(EmailChanged::class,      SendSystemNotification::class);
Event::listen(TwoFactorEnabled::class,  SendSystemNotification::class);
Event::listen(TwoFactorDisabled::class, SendSystemNotification::class);
Event::listen(UserRegistered::class, CreateDefaultNotificationPreferencesListener::class);
```

---

## User Preferences

Users control which notifications they receive and through which channels. Preferences are stored per `(user_id, notification_type_id)`. If no preference exists, defaults apply (`email: true`, `whatsapp: false`).

### Database — `notification_types`

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `uuid` | uuid | Public identifier |
| `key` | string | Unique event key (e.g. `auth.login`) |
| `description` | string | Human-readable description |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

### Database — `notification_preferences`

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `uuid` | uuid | Public identifier |
| `user_id` | bigint | FK → users |
| `notification_type_id` | bigint | FK → notification_types |
| `email` | boolean | Receive via email (default: true) |
| `whatsapp` | boolean | Receive via WhatsApp (default: false) |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

> Unique constraint on `(user_id, notification_type_id)`.

---

## Endpoints

All routes are prefixed with `/api/notifications`.
All responses follow the pattern `{ message, status, data }`.

---

### `GET /notifications/preferences` 🔒

Returns all notification types with the user's current preferences for each channel.

**Response** `200`
```json
{
    "message": "Notification preferences retrieved successfully.",
    "status": 200,
    "data": [
        {
            "uuid": "550e8400-...",
            "key": "auth.login",
            "description": "New login detected",
            "channels": {
                "email": true,
                "whatsapp": false
            }
        },
        {
            "uuid": "7c9e6679-...",
            "key": "auth.2fa_disabled",
            "description": "Two-factor authentication disabled",
            "channels": {
                "email": true,
                "whatsapp": true
            }
        }
    ]
}
```

---

### `PUT /notifications/preferences` 🔒

Updates the user's notification preferences. Accepts an array of preferences to update. Uses upsert — creates if not exists, updates if exists.

**Request**
```json
{
    "preferences": [
        {
            "notification_type_uuid": "550e8400-...",
            "channels": {
                "email": true,
                "whatsapp": true
            }
        }
    ]
}
```

**Response** `200`
```json
{
    "message": "Notification preferences updated successfully.",
    "status": 200,
    "data": [...]
}
```

---

## Email Layouts

### `base.blade.php`
Used for transactional emails: verify email, password reset, password changed, 2FA enabled, etc.
- Dark header (`#18181b`)
- Clean body with action button
- Standard footer

### `alert.blade.php`
Used for security alert emails: new login, 2FA disabled, email changed, device revoked, etc.
- Red header (`#dc2626`) with ⚠️ Security Alert title
- Red alert box for highlighting important warnings
- Meta table for contextual data (IP, device, date)
- Footer with support link

---

## Key Design Decisions

### Actions over Services
`SendNotificationAction` orchestrates channels and repositories — it is business logic, not an external integration. When WhatsApp is implemented, `WhatsAppChannel` will call a `WhatsAppService` (external API), but the orchestration stays in the Action.

### Queued listener
`SendSystemNotification` implements `ShouldQueue` — all notification dispatches are async. Never blocks the HTTP request.

### notifiable resolution
The listener resolves the notifiable from the event without requiring a fixed property name:
```php
$notifiable = $event->user ?? $event->notifiable ?? null;
```
This keeps events clean and avoids coupling all events to a single convention.

### Defaults without preferences
If a user has no preference row for a notification type, the system defaults to `email: true, whatsapp: false`. Preference rows are only created when the user explicitly changes their settings.

---

## Conventions

### NotificationDTO

Carries all data needed by channels:

```php
new NotificationDTO(
    type:     'auth.login',
    subject:  'New login detected',
    template: 'notification::emails.auth.login-detected',
    data: [
        'name'      => $user->name,
        'ip'        => '192.168.1.1',
        'device'    => 'Chrome - Windows',
        'logged_at' => '2026-03-13 10:00:00',
    ],
);
```

### Adding a new notification type

1. Add entry to `NotificationTypeSeeder` and re-seed
2. Create Blade template in `Resources/views/emails/{module}/`
3. Add case to `SendSystemNotification::resolveDTO()`
4. Register the event listener in the module's ServiceProvider

### Adding a new channel

1. Create `NewChannel.php` implementing `NotificationChannelInterface`
2. Add boolean column to `notification_preferences` via migration
3. Register channel in `SendNotificationAction::$channels`
4. The preferences endpoint automatically includes the new channel
