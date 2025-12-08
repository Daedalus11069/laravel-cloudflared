<?php

namespace PixoVoid\Cloudflared;

use PixoVoid\Cloudflared\Data\ProjectConfig;
use PixoVoid\Cloudflared\Data\TunnelConfig;

class Cloudflared
{
    public function isInstalled(): bool
    {
        return ProjectConfig::exists();
    }

    public function projectConfig(): ProjectConfig
    {
        return once(fn () => ProjectConfig::load());
    }

    public function tunnelConfig(): TunnelConfig
    {
        return once(fn () => new TunnelConfig($this->projectConfig()));
    }
}
