# OI Laravel Metadata — AI Context

This package provides polymorphic SEO **metadata** and **Open Graph** objects for Laravel applications. Any
Eloquent model can carry at most one metadata record and one Open Graph record, rendered into `<head>` meta
tags. Global/site-wide values are resolved from the host application `Setting` model when available.

## Core Concepts

- **Metadata** — one-per-parent polymorphic record holding `title`, `description`, `keywords[]`, `author`,
  `copyright`, `language` (ISO), `revisit_after`, `robots`, `googlebot`. Lives in the `metadata` table.
- **OpenGraph** — one-per-parent polymorphic record holding `type`, `title`, `description`, `url`, and an
  `image` object (`url`, `width`, `height`). Lives in the `open_graphs` table.
- **One per parent** — both relations are `morphOne`, with a unique index on the morph columns.
- **DTOs** — `MetadataData`, `OpenGraphData`, `OpenGraphImageData` (spatie/laravel-data).
- **Services** — `MetaService` and `OgService` (facades `Meta` and `Og`) read, write, and render.
- **Traits** — `HasMetadata`, `HasOpenGraph`, and the combined `HasMeta`.

## Adding Metadata & Open Graph to a Model

```php
use OiLab\OiLaravelMetadata\Concerns\HasMeta;

class Page extends Model
{
    use HasMeta; // or HasMetadata / HasOpenGraph individually
}
```

```php
$page->metadata;   // MorphOne — at most one Metadata
$page->openGraph;  // MorphOne — at most one OpenGraph
```

## Writing Values

Use the DTOs and services (or the trait helpers):

```php
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Data\OpenGraphImageData;
use OiLab\OiLaravelMetadata\Facades\Meta;
use OiLab\OiLaravelMetadata\Facades\Og;

Meta::update($page, new MetadataData(
    title: 'About us',
    description: 'Who we are',
    keywords: ['team', 'company'],
    language: 'fr',
    robots: 'index, follow',
));

Og::update($page, new OpenGraphData(
    type: 'website',
    title: 'About us',
    url: 'https://example.com/about',
    image: new OpenGraphImageData(url: 'https://example.com/og.png', width: 1200, height: 630),
));

// Trait helpers do the same:
$page->syncMetadata(new MetadataData(title: 'About us'));
$page->syncOpenGraph(new OpenGraphData(type: 'article'));
```

## Rendering `<head>` Tags

```blade
{!! Meta::render($page) !!}
{!! Og::render($page) !!}
```

`Meta::render()` emits `description`, `keywords`, `author`, `copyright`, `language`, `revisit-after`,
`robots`, `googlebot`, plus site verification tags from settings (`google-site-verification`, `google`).
`Og::render()` emits `og:*` plus `og:locale`, `og:site_name` and `fb:app_id` resolved from settings.

## Setting Model Integration

When the host app exposes a key/value `Setting` model, seed defaults with:

```bash
php artisan metadata:install-settings
```

Seeded keys (idempotent, existing keys untouched): `METADATA_FACEBOOK_APP_ID`,
`METADATA_GOOGLE_SITE_VERIFICATION`, `METADATA_GOOGLE_BOT`, `METADATA_GOOGLE`,
`METADATA_ROBOTS` (`index, follow`), `METADATA_OG_LOCALE` (`fr`), `METADATA_OG_SITE_NAME`,
`METADATA_OG_TYPE` (`website`). The model class and key/value columns are configurable under
`config/oi-laravel-metadata.php` → `settings`. When no Setting model is present, the resolver falls back to
the config defaults.

## Validation

- **Form requests**: `MetadataRequest`, `OpenGraphRequest`.
- **Rules**: `IsoLanguageRule` (e.g. `fr`, `en`, `fr-FR`), `RobotsRule` (e.g. `index, follow`).

## Conventions

- Always resolve model classes through `OiMetadata` (`OiMetadata::metadataModel()`,
  `OiMetadata::openGraphModel()`), never reference the concrete models directly.
- Both relations are `morphOne`; writes use `updateOrCreate` so a parent never gets a second record.
- `keywords` is stored as JSON (array cast); `image` is stored as JSON (array cast).

## Configuration

```bash
php artisan vendor:publish --tag=oi-laravel-metadata-config
php artisan vendor:publish --tag=oi-laravel-metadata-migrations
php artisan migrate
```

## Updating the AI Skill

```bash
php artisan oi:skills
```
