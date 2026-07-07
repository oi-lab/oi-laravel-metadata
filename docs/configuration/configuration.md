---
title: Configuration
description: Every option in config/oi-laravel-metadata.php
section: configuration
order: 1
---

# Configuration

Publish the config file to customize the package:

```bash
php artisan vendor:publish --tag=oi-laravel-metadata-config
```

This creates `config/oi-laravel-metadata.php`.

## Models

The model classes used by the package. Override them with your own subclasses — always resolved through the
`OiMetadata` helper so overrides apply everywhere. See [Custom Models](../advanced/custom-models.md).

```php
'models' => [
    'metadata' => OiLab\OiLaravelMetadata\Models\Metadata::class,
    'open_graph' => OiLab\OiLaravelMetadata\Models\OpenGraph::class,
    'json_ld' => OiLab\OiLaravelMetadata\Models\JsonLd::class,
],
```

## JSON-LD

Options for rendering JSON-LD `<script type="application/ld+json">` blocks. See
[Managing JSON-LD](../usage/json-ld.md).

```php
'json_ld' => [
    'context' => 'https://schema.org',
    'pretty' => false,
],
```

| Key | Default | Purpose |
|-----|---------|---------|
| `context` | `https://schema.org` | The `@context` injected into each top-level graph that does not declare its own |
| `pretty` | `false` | Pretty-print the JSON (useful while debugging; keep it compact in production) |

## Automatic subject resolution

Controls whether the `@meta`, `@og`, and `@jsonLd` Blade directives — called with no argument — fall back to
the current route's bound model when no subject was set via `Seo::for($model)`. See
[Rendering tags](../usage/rendering.md#rendering-without-passing-page).

```php
'auto_resolve_subject' => true,
```

| Key | Default | Purpose |
|-----|---------|---------|
| `auto_resolve_subject` | `true` | Auto-resolve the directives' subject from the last route-bound model exposing the relevant relation |

## Defaults

Fallback values used by `MetaService` when a model has no record or leaves a field empty.

```php
'defaults' => [
    'language' => 'fr',
    'robots' => 'index, follow',
    'revisit_after' => '7 days',
],
```

| Key | Default | Used for |
|-----|---------|----------|
| `language` | `fr` | `<meta name="language">` fallback |
| `robots` | `index, follow` | `<meta name="robots">` fallback |
| `revisit_after` | `7 days` | `<meta name="revisit-after">` fallback |

## Settings

Integration with the host application's key/value `Setting` model. The installer seeds these defaults, and the
resolver reads global values back out of them when rendering. See
[Setting Integration](../advanced/settings-integration.md).

```php
'settings' => [
    'model' => 'App\\Models\\Setting',
    'key_column' => 'key',
    'value_column' => 'value',

    'defaults' => [
        'METADATA_FACEBOOK_APP_ID' => '',
        'METADATA_GOOGLE_SITE_VERIFICATION' => '',
        'METADATA_GOOGLE_BOT' => '',
        'METADATA_GOOGLE' => '',
        'METADATA_ROBOTS' => 'index, follow',
        'METADATA_OG_LOCALE' => 'fr',
        'METADATA_OG_SITE_NAME' => '',
        'METADATA_OG_TYPE' => 'website',
    ],
],
```

| Key | Type | Purpose |
|-----|------|---------|
| `model` | class-string | The host `Setting` model class. When it doesn't exist, settings features no-op and fall back to `defaults`. |
| `key_column` | string | Column holding the setting key |
| `value_column` | string | Column holding the setting value |
| `defaults` | array | The default key/value pairs the installer seeds and the resolver falls back to |
