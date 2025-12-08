<?php

namespace PixoVoid\Cloudflared\Console\Commands;

use Illuminate\Console\Command;
use PixoVoid\Cloudflared\Concerns\InteractsWithCloudflareApi;
use PixoVoid\Cloudflared\Concerns\InteractsWithHerd;
use PixoVoid\Cloudflared\Concerns\InteractsWithTunnel;
use PixoVoid\Cloudflared\Concerns\ManagesProject;
use PixoVoid\Cloudflared\Facades\Cloudflared;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;

class CloudflaredUninstall extends Command
{
    use InteractsWithCloudflareApi, InteractsWithHerd, InteractsWithTunnel, ManagesProject;

    protected $signature = 'cloudflared:uninstall';

    protected $description = 'Delete the Cloudflare Tunnel of this project.';

    public function handle()
    {
        $this->verifyCloudflaredFoundInPath();
        $this->verifyHerdFoundInPath();

        if (! Cloudflared::isInstalled()) {
            $this->fail('No project configuration found. Run "php artisan cloudflared:install" first.');
        }

        $tunnelConfig = Cloudflared::tunnelConfig();

        $confirmed = confirm(
            label: "Are you sure you want to delete tunnel {$tunnelConfig->name()}?",
            hint: 'Deletes the tunnel, DNS records, Herd link, and all associated configs.',
            default: false,
        );

        if (! $confirmed) {
            error(' ⚠ Cancelled.');

            return self::SUCCESS;
        }

        $this->deleteTunnel($tunnelConfig->name());
        $this->deleteHerdLink($tunnelConfig->hostname());
        $this->deleteDnsRecords($tunnelConfig);
        $this->deleteProject($tunnelConfig);
    }
}
