<?php

return [
    App\Providers\AppServiceProvider::class,

    // ─── Module Providers ───────────────────────────────
    App\Modules\Auth\AuthServiceProvider::class,
    App\Modules\Notification\NotificationServiceProvider::class,
    // App\Modules\Properties\PropertiesServiceProvider::class,
];
