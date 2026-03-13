<?php

namespace App\Shared\Helpers;

use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(
        string $event,
        string $logName = 'auth',
        ?object $subject = null,
        array $properties = []
    ): void {
        $logger = activity($logName)
            ->withProperties($properties)
            ->event($event);

        if ($subject) {
            $logger->performedOn($subject);
        }

        $causer = Auth::check() ? Auth::user() : $subject;

        if ($causer) {
            $logger->causedBy($causer);
        }

        $logger->log($event);
    }
}
