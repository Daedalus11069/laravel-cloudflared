<?php

namespace PixoVoid\Cloudflared\Support;

class Platform
{
    public static function homeDirectory(): string
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE');

        if ($home === false || $home === null || $home === '') {
            // Fallback to HOMEDRIVE + HOMEPATH (Windows)
            $home = rtrim((string) (getenv('HOMEDRIVE') . getenv('HOMEPATH')), "\\/") ?: 'C:/';
        }

        return str_replace('\\', '/', $home);
    }

    public static function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
