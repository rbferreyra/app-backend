<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Auth
            ['key' => 'auth.login',                 'description' => 'New login detected'],
            ['key' => 'auth.password_changed',       'description' => 'Password changed'],
            ['key' => 'auth.email_changed',          'description' => 'Email address changed'],
            ['key' => 'auth.2fa_enabled',            'description' => 'Two-factor authentication enabled'],
            ['key' => 'auth.2fa_disabled',           'description' => 'Two-factor authentication disabled'],
            ['key' => 'auth.device_revoked',         'description' => 'A device was revoked'],

            // Property
            ['key' => 'property.published',          'description' => 'Property published'],
            ['key' => 'property.archived',           'description' => 'Property archived'],

            // Billing
            ['key' => 'billing.invoice_due',         'description' => 'Invoice due'],
            ['key' => 'billing.payment_confirmed',   'description' => 'Payment confirmed'],
            ['key' => 'billing.subscription_canceled', 'description' => 'Subscription canceled'],
        ];

        foreach ($types as $type) {
            DB::table('notification_types')->updateOrInsert(
                ['key' => $type['key']],
                [
                    'uuid' => Str::uuid(),
                    'description' => $type['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
