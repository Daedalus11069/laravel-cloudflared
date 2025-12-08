<?php

namespace PixoVoid\Cloudflared\Tests;

use Orchestra\Testbench\TestCase;
use PixoVoid\Cloudflared\CloudflaredServiceProvider;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [CloudflaredServiceProvider::class];
    }

    public function test_service_provider_registers_commands()
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);

        $commands = $kernel->all();

        $this->assertArrayHasKey('cloudflared:install', $commands);
        $this->assertArrayHasKey('cloudflared:run', $commands);
        $this->assertArrayHasKey('cloudflared:uninstall', $commands);
    }
}
