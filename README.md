# Laravel Cloudflared

A Laravel package for creating and managing Cloudflare Tunnels to provide secure public access to local development environments. This solution offers an alternative to services like ngrok or Expose, leveraging Cloudflare's infrastructure for tunneling requests directly to your local Laravel application.

## Key Features

- Create and manage Cloudflare Tunnels directly from Laravel Artisan commands
- Automatic DNS record configuration for custom subdomains
- Integration with Laravel Herd for local development environments
- Secure tunneling with Cloudflare's global network
- Seamless integration with Vite development server via companion package

## Prerequisites

Before using this package, ensure you have:

1. **Cloudflared CLI**: Install from [Cloudflare's downloads page](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads)
2. **Cloudflare Authentication**: Run `cloudflared tunnel login` to authenticate with your Cloudflare account and domain
3. **Laravel Herd**: Install [Laravel Herd](https://herd.laravel.com) for optimal local development experience

## Installation

Install the package via Composer:

```bash
composer require pixovoid/laravel-cloudflared
```

## Usage

### Creating a Tunnel

Create a Cloudflare tunnel for your project with a single command. This command will:
- Create a new Cloudflare tunnel
- Configure DNS records for your specified subdomain
- Set up a Herd link for local access
- Save configuration to `.cloudflared.yaml` in your project root

```bash
php artisan cloudflared:install
```

**Note**: Running this command again will modify the existing installation. You can change the subdomain, repair DNS records, or recreate the tunnel as needed.

### Running the Tunnel

Start the tunnel to make your local development environment publicly accessible:

```bash
php artisan cloudflared:run
```

### Removing a Tunnel

Clean up all resources when the tunnel is no longer needed:

```bash
php artisan cloudflared:uninstall
```

## Development Setup

### Local Development with Path Repository

For developing or modifying the package, you can use Composer's path repository:

1. Add the path repository to your Laravel application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../path/to/laravel-cloudflared",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

2. Require the package from the local path:

```bash
composer require pixovoid/laravel-cloudflared:dev-main
```

### Testing

The package includes PHPUnit tests and uses `orchestra/testbench` for Laravel integration testing.

Run the test suite after installing development dependencies:

```bash
composer install --dev
composer test
```

## Platform Compatibility

### Windows Support

This fork implements best-effort Windows compatibility with the following considerations:

- Automatic detection and fallback for TTY unsupported environments
- Normalized home directory detection supporting `HOME`, `USERPROFILE`, and `HOMEDRIVE`/`HOMEPATH` environment variables
- Graceful fallback for process signal handling when `pcntl` extension is unavailable
- Process cleanup mechanisms for Windows environments

For Windows usage, ensure:
- `cloudflared` CLI is installed and available in your system PATH
- Laravel Herd is installed and configured
- Required environment variables are properly set

## Attribution and Licensing

This repository is a fork of the original project `aerni/laravel-cloudflared` (https://github.com/aerni/laravel-cloudflared).

### Original Project
- **Author**: Michael Aerni
- **License**: MIT
- **Repository**: https://github.com/aerni/laravel-cloudflared

### Fork Maintainer
- **Maintainer**: PixoVoid (PixoVoid.dev, PixoVoid.net)
- **Package Name**: `pixovoid/laravel-cloudflared`
- **Key Changes**: Windows compatibility improvements, maintenance updates, and platform-specific fixes

### License Information

This software is licensed under the MIT License. See the `LICENSE` file for the full license text.

Copyright for the original work belongs to Michael Aerni. PixoVoid is listed as the fork maintainer.

For a detailed summary of changes made in this fork, please refer to the `NOTICE.md` file.

## Support

For issues, feature requests, or questions, please use the [GitHub Issues](https://github.com/PixoVoid/laravel-cloudflared/issues) page of this repository.

## Related Packages

For seamless integration with Vite development server, consider using [Cloudflared for Vite](https://github.com/aerni/vite-plugin-laravel-cloudflared), which provides tunneled access to both your Laravel application and Vite's hot module replacement server, facilitating frontend debugging on real devices.