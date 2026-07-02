---
title: Setting Integration
description: Seeding and resolving site-wide values through a pluggable SettingStore
section: advanced
order: 1
---

# Setting Integration

Some metadata is site-wide rather than per-model: the Facebook App ID, Google verification tokens, the default
Open Graph locale and site name. The package reads and seeds these through a pluggable `SettingStore`, so it
never hard-depends on any particular settings implementation. It prefers `oi-lab/oi-laravel-settings` when
installed, falls back to a generic key/value `Setting` model otherwise, and falls back to config defaults when
no store is available.

## The settings

| Key | Default | Rendered as |
|-----|---------|-------------|
| `METADATA_FACEBOOK_APP_ID` | `""` | `<meta property="fb:app_id">` |
| `METADATA_GOOGLE_SITE_VERIFICATION` | `""` | `<meta name="google-site-verification">` |
| `METADATA_GOOGLE_BOT` | `""` | `<meta name="googlebot">` fallback |
| `METADATA_GOOGLE` | `""` | `<meta name="google">` |
| `METADATA_ROBOTS` | `index, follow` | `<meta name="robots">` fallback |
| `METADATA_OG_LOCALE` | `fr` | `<meta property="og:locale">` |
| `METADATA_OG_SITE_NAME` | `""` | `<meta property="og:site_name">` |
| `METADATA_OG_TYPE` | `website` | `og:type` fallback when none is set |

## Choosing the backend

The store is resolved on every call, in this order:

1. **Explicit** — a class bound via `config('oi-laravel-metadata.settings.store')`.
2. **oi-laravel-settings** — used automatically when the package is installed (recommended). Values are stored
   scoped and typed in the shared Setting store.
3. **Config model** — the generic key/value fallback (`settings.model` + `key_column` / `value_column`), for
   hosts with their own `Setting` table.

`oi-lab/oi-laravel-settings` is listed in the package's `suggest`. Install it for zero-config, first-class
settings:

```bash
composer require oi-lab/oi-laravel-settings
```

## Seeding the settings

Run the installer command:

```bash
php artisan metadata:install-settings
```

It is **idempotent**: existing keys are never overwritten, only missing ones are created. When no usable
store exists, it prints a warning and exits successfully without touching anything.

```text
Created setting: METADATA_FACEBOOK_APP_ID
Created setting: METADATA_GOOGLE_SITE_VERIFICATION
...
Installed 8 metadata setting(s).
```

## The config-model fallback

When `oi-lab/oi-laravel-settings` is absent, the package reads the generic key/value model from
`config('oi-laravel-metadata.settings')`:

```php
'settings' => [
    // Explicit SettingStore implementation (class-string), or null to auto-detect.
    'store' => null,
    'model' => App\Models\Setting::class,
    'key_column' => 'key',
    'value_column' => 'value',
],
```

A model is "usable" when its class exists **and** its database table exists. The `key_column` /
`value_column` make the package work with most key/value schemas without changing your model.

## Resolving values in code

The `SettingResolver` reads a value, falling back to the config default for the key:

```php
use OiLab\OiLaravelMetadata\Support\SettingResolver;

$locale = app(SettingResolver::class)->get('METADATA_OG_LOCALE'); // 'fr' by default
```

The `MetaService` and `OgService` use it internally when rendering, so you rarely call it directly. Both
`SettingResolver` and `SettingsInstaller` are thin façades over the resolved `SettingStore`.

## Seeding programmatically

The installer is also available as a service:

```php
use OiLab\OiLaravelMetadata\Support\SettingsInstaller;

$installer = app(SettingsInstaller::class);

if ($installer->canInstall()) {
    $created = $installer->install(); // list<string> of created keys
}
```

This is handy inside your own deploy or seed routines.

## Custom store

Implement `OiLab\OiLaravelMetadata\Contracts\SettingStore` and point the config at it (a class name or
container binding key):

```php
'settings' => [
    'store' => \App\Settings\MyMetadataStore::class,
],
```
