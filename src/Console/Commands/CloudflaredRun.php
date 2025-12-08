<?php

namespace PixoVoid\Cloudflared\Console\Commands;

use PixoVoid\Cloudflared\Concerns\InteractsWithHerd;
use PixoVoid\Cloudflared\Concerns\InteractsWithTunnel;
use PixoVoid\Cloudflared\Concerns\ManagesProject;
use PixoVoid\Cloudflared\Support\Platform;
use PixoVoid\Cloudflared\Data\TunnelConfig;
use PixoVoid\Cloudflared\Facades\Cloudflared;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\info;

class CloudflaredRun extends Command
{
    use InteractsWithHerd, InteractsWithTunnel, ManagesProject;

    protected $signature = 'cloudflared:run';

    protected $description = 'Run the Cloudflare Tunnel of this project.';

    public function handle(): void
    {
        $this->verifyCloudflaredFoundInPath();
        $this->verifyHerdFoundInPath();

        if (! Cloudflared::isInstalled()) {
            $this->fail('No project configuration found. Run "php artisan cloudflared:install" first.');
        }

        $tunnelConfig = Cloudflared::tunnelConfig();

        $this->saveTunnelConfig($tunnelConfig);
        $this->createHerdLink($tunnelConfig->hostname());
        $this->runCloudflared($tunnelConfig);
    }

    // TODO: Only show process output if it was requested via a --debug or --logLevel or something like that.
    // Else, only show errors.

    // TODO: Look through this and see if there is anything to optimize.
    protected function runCloudflared(TunnelConfig $tunnelConfig): void
    {
        info(' ✔ Started tunnel.');

        $pending = Process::forever();

        // Only enable TTY when the platform and environment support it.
        if (! Platform::isWindows() && \Symfony\Component\Process\Process::isTtySupported()) {
            $pending = $pending->tty();
        }

        $process = $pending->start("cloudflared tunnel --config {$tunnelConfig->path()} run");

        // If pcntl is available (typical on Unix) we rely on it for signal handling.
        if (! Platform::isWindows() && function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);

            $shuttingDown = false;

            $signalHandler = function ($signal) use ($process, &$shuttingDown, $tunnelConfig) {
                if ($shuttingDown) {
                    return;
                }

                $shuttingDown = true;

                if ($process->running()) {
                    $process->signal(SIGTERM);
                    $process->wait();
                }

                $this->cleanupCloudflaredProcess($tunnelConfig);
                exit(0);
            };

            pcntl_signal(SIGINT, $signalHandler);  // Ctrl+C
            pcntl_signal(SIGTERM, $signalHandler); // Termination signal
            pcntl_signal(SIGHUP, $signalHandler);  // Hangup signal

            try {
                $process->wait();

                if (! $shuttingDown) {
                    $this->cleanupCloudflaredProcess($tunnelConfig);
                }
            } catch (\Exception $e) {
                if ($process->running() && ! $shuttingDown) {
                    $process->signal(SIGTERM);
                    $process->wait();
                }

                if (! $shuttingDown) {
                    $this->cleanupCloudflaredProcess($tunnelConfig);
                }

                throw $e;
            }
        } else {
            // Windows / no-pcntl fallback: register shutdown function to ensure cleanup.
            register_shutdown_function(function () use ($process, $tunnelConfig) {
                try {
                    if (is_object($process)) {
                        if (method_exists($process, 'stop')) {
                            $process->stop();
                        } elseif (method_exists($process, 'signal')) {
                            // Try to send SIGTERM if available; may be a no-op on Windows.
                            @$process->signal(15);
                        }

                        if (method_exists($process, 'wait')) {
                            @$process->wait();
                        }
                    }
                } catch (\Throwable $e) {
                    // swallow
                }

                // Best-effort cleanup: directly delete the tunnel config file.
                try {
                    if (method_exists($tunnelConfig, 'delete')) {
                        @$tunnelConfig->delete();
                    }
                } catch (\Throwable $e) {
                    // swallow
                }
            });

            // Block and forward process output; when process exits the shutdown handler will run.
            $process->wait();
        }
    }

    protected function cleanupCloudflaredProcess(TunnelConfig $tunnelConfig): void
    {
        info(' ✔ Stopped tunnel.');

        $this->deleteTunnelConfig($tunnelConfig);
    }
}
