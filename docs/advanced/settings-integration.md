---
title: Setting Integration
description: Seeding and resolving site-wide values from a host Setting model
section: advanced
order: 1
---

# Setting Integration

Some metadata is site-wide rather than per-model: the Facebook App ID, Google verification tokens, the default
Open Graph locale and site name. The package reads these from the host application's key/value `Setting` model
when one is available, and falls back to config defaults otherwise — so it never hard-depends on any particular
settings implementation.

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

## Seeding the settings

Run the installer command:

```bash
php artisan metadata:install-settings
```

It is **idempotent**: existing keys are never overwritten, only missing ones are created. When no usable
`Setting` model exists, it prints a warning and exits successfully without touching anything.

```text
Created setting: METADATA_FACEBOOK_APP_ID
Created setting: METADATA_GOOGLE_SITE_VERIFICATION
...
Installed 8 metadata setting(s).
```

## How the Setting model is detected

The package reads `config('oi-laravel-metadata.settings')`:

```php
'settings' => [
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

The `MetaService` and `OgService` use it internally when rendering, so you rarely call it directly.

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
