# Cloudflared for Laravel

A simple package to create and manage Cloudflare Tunnels for your Laravel projects. Cloudflare Tunnels give you instant public access to your local development environment, similar to Expose or ngrok, but powered by Cloudflare. Perfect for testing webhooks and sharing work-in-progress.

Pair it with [Cloudflared for Vite](https://github.com/aerni/vite-plugin-laravel-cloudflared) to get seamless tunneled access to both your Laravel app and Vite's dev server, making it effortless to debug your frontend on real devices like your iPhone.

## Prerequisites

1. Install [cloudflared](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads)
2. Run `cloudflared tunnel login` to authenticate the desired domain
3. Install [Laravel Herd](https://herd.laravel.com)

## Installation

Install the package using Composer (fork package):

```bash
composer require pixovoid/laravel-cloudflared
```

## Basic Usage

### Creating a tunnel

Create a tunnel for your project with a single command. This will create a Cloudflare tunnel, configure DNS records, set up a Herd link, and save the configuration to `.cloudflared.yaml` in your project root.

```bash
php artisan cloudflared:install
```

> **Note:** Run this command again to modify the existing installation. Change the subdomain, create or repair DNS records, or delete and recreate the tunnel.

### Running the tunnel

Start the tunnel to make your local site publicly accessible.

```bash
php artisan cloudflared:run
```

### Deleting the tunnel

Remove the tunnel, DNS records, and configuration when you no longer need it.

```bash
php artisan cloudflared:uninstall
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Credits

Developed by [Michael Aerni](https://michaelaerni.ch)

Fork maintained by [PixoVoid](https://PixoVoid.dev) (PixoVoid.dev, PixoVoid.net)

## Support

For issues and questions, please use the [GitHub Issues](https://github.com/PixoVoid-net/laravel-cloudflared/issues) page.

## Attribution & Notice

This repository is a fork of the original project `aerni/laravel-cloudflared` (https://github.com/aerni/laravel-cloudflared). The original author is Michael Aerni and his work is licensed under MIT. This fork is maintained by PixoVoid and includes additional platform compatibility fixes and maintenance.

See `NOTICE.md` for a short summary of the fork changes and attribution.

### License & Copyright

The original license (MIT) is retained. See the `LICENSE` file for full license text. The original copyright belongs to Michael Aerni; PixoVoid is listed as the fork maintainer.

### Installation (Composer)

Recommended (Packagist):

```bash
composer require pixovoid/laravel-cloudflared
```

Developing locally (use `path` repository in your app to work on the package inline):

1. In your Laravel app's `composer.json` add:

```json
"repositories": [
	{
		"type": "path",
		"url": "../path/to/laravel-cloudflared",
		"options": {"symlink": true}
	}
]
```

2. From your Laravel app folder run:

```bash
composer require pixovoid/laravel-cloudflared:dev-main
```

### Testing

This package includes PHPUnit tests and uses `orchestra/testbench` for Laravel integration tests.

Run tests locally after installing dev dependencies:

```bash
composer install --dev
composer test
```

### Windows notes

This fork implements best-effort Windows compatibility:

- Avoids using TTY on Windows and checks `Process::isTtySupported()`.
- Uses a normalized `Platform::homeDirectory()` to support `HOME`, `USERPROFILE` and `HOMEDRIVE`+`HOMEPATH`.
- When `pcntl` is not available (common on Windows), a shutdown fallback is used to attempt cleanup of the `cloudflared` process and tunnel config.

If you plan to run `php artisan cloudflared:run` on Windows, ensure `cloudflared` and `herd` are installed and available in `%PATH%`.

## Fork notes (PixoVoid)

This repository is a fork of the original `aerni/laravel-cloudflared` with additional Windows compatibility fixes and maintenance by PixoVoid. Key fork points:

- Windows path and TTY fallbacks for `cloudflared:run`.
- `Platform::homeDirectory()` to normalize HOME/USERPROFILE/HOMEDRIVE handling.
- Composer package republished under `pixovoid/laravel-cloudflared`.

If you rely on the original package name (`aerni/cloudflared`), note that this fork uses a different package name and must be required explicitly.

### Windows notes

This fork adds best-effort compatibility for Windows 11. Notes:

- `pcntl` and Unix signals are not available on Windows; the package uses a shutdown fallback for cleanup when `pcntl` is not present.
- TTY is not supported on Windows; the package avoids calling `->tty()` on Windows.
- `cloudflared` and `herd` must be installed and in your `%PATH%` for CLI commands that call those binaries.

For full Windows testing, run the commands on a Windows 11 machine with `cloudflared` and `herd` installed, or mock the external calls in tests.
