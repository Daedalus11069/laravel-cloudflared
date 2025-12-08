<?php

namespace PixoVoid\Cloudflared\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isInstalled()
 * @method static \PixoVoid\Cloudflared\Data\ProjectConfig projectConfig()
 * @method static \PixoVoid\Cloudflared\Data\TunnelConfig tunnelConfig()
 *
 * @see \PixoVoid\Cloudflared\Cloudflared
 */
class Cloudflared extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \PixoVoid\Cloudflared\Cloudflared::class;
    }
}
