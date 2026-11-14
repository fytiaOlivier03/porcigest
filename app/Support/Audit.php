<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/** Journal d'audit dédié : storage/logs/audit.log */
class Audit
{
    public static function info(string $message, array $context = []): void
    {
        self::ecrire('info', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::ecrire('warning', $message, $context);
    }

    private static function ecrire(string $niveau, string $message, array $context): void
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/audit.log')])
            ->{$niveau}($message, ['user_id' => Auth::id()] + $context);
    }
}
