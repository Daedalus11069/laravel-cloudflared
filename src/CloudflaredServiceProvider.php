<?php

namespace PixoVoid\Cloudflared;

use Illuminate\Support\ServiceProvider;
use PixoVoid\Cloudflared\Clients\CloudflareClient;
use PixoVoid\Cloudflared\Console\Commands\CloudflaredInstall;
use PixoVoid\Cloudflared\Console\Commands\CloudflaredRun;
use PixoVoid\Cloudflared\Console\Commands\CloudflaredUninstall;
use PixoVoid\Cloudflared\Data\Certificate;
use PixoVoid\Cloudflared\Facades\Cloudflared;

class CloudflaredServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCloudflareClient();
        $this->setAppUrl();
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'cloudflared');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CloudflaredInstall::class,
                CloudflaredRun::class,
                CloudflaredUninstall::class,
            ]);
        }
    }

    protected function registerCloudflareClient(): void
    {
        $this->app->singleton(CloudflareClient::class, fn () => new CloudflareClient(Certificate::load()));
    }

    protected function setAppUrl(): void
    {
        // Avoid touching request/config when running in the console.
        if ($this->app->runningInConsole()) {
            return;
        }

        if (! Cloudflared::isInstalled()) {
            return;
        }

        $request = request();

        if (! $request || $request->host() !== Cloudflared::tunnelConfig()->hostname()) {
            return;
        }

        config()->set('app.url', Cloudflared::tunnelConfig()->url());
    }
}
