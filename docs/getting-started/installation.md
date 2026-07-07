---
title: Installation
description: How to install OI Laravel Metadata via Composer
section: getting-started
order: 2
---

# Installation

## Via Composer

```bash
composer require oi-lab/oi-laravel-metadata
```

The package auto-discovers and registers its service provider via Laravel's package discovery — no manual
registration required. It depends on `spatie/laravel-data`, which is installed automatically.

## Run the migrations

The package ships the `metadata`, `open_graphs`, and `json_ld` migrations. They are loaded automatically from
the package, so `php artisan migrate` works even without publishing. Publish them only when you need to
customize the schema:

```bash
php artisan vendor:publish --tag=oi-laravel-metadata-migrations
php artisan migrate
```

This creates the `metadata`, `open_graphs`, and `json_ld` tables, each with a unique index on the morph columns
so a parent never gets a second record.

## Publish the configuration (optional)

```bash
php artisan vendor:publish --tag=oi-laravel-metadata-config
```

This creates `config/oi-laravel-metadata.php` with sensible defaults. See
[Configuration](../configuration/configuration.md) for all available options.

## Seed default settings (optional)

If your application exposes a key/value `Setting` model, seed the package's default metadata settings:

```bash
php artisan metadata:install-settings
```

The command is idempotent and no-ops gracefully when no `Setting` model is present. See
[Setting Integration](../advanced/settings-integration.md) for details.

## Local development

To use the package from a local checkout alongside your project, add a `path` repository to your project's
`composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/oi-lab/oi-laravel-metadata"
        }
    ]
}
```

Then `composer require oi-lab/oi-laravel-metadata`.
