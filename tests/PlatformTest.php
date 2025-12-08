<?php

namespace PixoVoid\Cloudflared\Tests;

use PHPUnit\Framework\TestCase;
use PixoVoid\Cloudflared\Support\Platform;

class PlatformTest extends TestCase
{
    protected function tearDown(): void
    {
        // clean env modifications
        putenv('HOME');
        putenv('USERPROFILE');
        putenv('HOMEDRIVE');
        putenv('HOMEPATH');
        parent::tearDown();
    }

    public function test_home_directory_prefers_home()
    {
        putenv('HOME=C:/Users/test-home');
        putenv('USERPROFILE=C:/Users/userprofile');

        $home = Platform::homeDirectory();

        $this->assertStringContainsString('C:/Users/test-home', $home);
    }

    public function test_home_directory_falls_back_to_userprofile()
    {
        putenv('HOME=');
        putenv('USERPROFILE=C:/Users/userprofile');

        $home = Platform::homeDirectory();

        $this->assertStringContainsString('C:/Users/userprofile', $home);
    }

    public function test_home_directory_falls_back_to_homedrive()
    {
        putenv('HOME=');
        putenv('USERPROFILE=');
        putenv('HOMEDRIVE=C:');
        putenv('HOMEPATH=\\Users\\fallback');

        $home = Platform::homeDirectory();

        $this->assertStringContainsString('C:/Users/fallback', $home);
    }
}
