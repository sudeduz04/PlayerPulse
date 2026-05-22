<?php

namespace App\Support;

class StatusLabels
{
    public const LINEUP = [
        'queued' => 'Sıraya alındı',
        'running' => 'İşleniyor',
        'completed' => 'Hazır',
        'failed' => 'Başarısız',
    ];

    public const FIXTURE_IMPORT = [
        'queued' => 'Sıraya alındı',
        'running' => 'İşleniyor',
        'completed' => 'Tamamlandı',
        'failed' => 'Başarısız',
    ];

    public const ANALYSIS = [
        'queued' => 'Sıraya alındı',
        'running' => 'İşleniyor',
        'completed' => 'Hazır',
        'failed' => 'Başarısız',
    ];

    public const MATCH_STATUS = [
        'scheduled' => 'Programlandı',
        'first_half' => 'İlk yarı',
        'half_time' => 'Devre arası',
        'second_half' => 'İkinci yarı',
        'finished' => 'Tamamlandı',
        'postponed' => 'Ertelendi',
    ];

    public static function lineup(?string $status): string
    {
        return self::LINEUP[$status] ?? ($status ?? '-');
    }

    public static function fixtureImport(?string $status): string
    {
        return self::FIXTURE_IMPORT[$status] ?? ($status ?? '-');
    }

    public static function analysis(?string $status): string
    {
        return self::ANALYSIS[$status] ?? ($status ?? '-');
    }

    public static function matchStatus(?string $status): string
    {
        return self::MATCH_STATUS[$status] ?? ($status ?? '-');
    }

    public static function badgeClasses(?string $status): string
    {
        return match ($status) {
            'completed', 'finished' => 'bg-green-500/15 text-green-400',
            'failed', 'postponed' => 'bg-red-500/15 text-red-400',
            'running', 'first_half', 'second_half' => 'bg-blue-500/15 text-blue-300',
            'half_time' => 'bg-orange-500/15 text-orange-300',
            'queued', 'scheduled' => 'bg-yellow-500/15 text-yellow-300',
            default => 'bg-gray-500/15 text-gray-300',
        };
    }
}
