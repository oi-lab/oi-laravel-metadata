# OI Laravel Metadata — AI Context

This package provides polymorphic SEO **metadata**, **Open Graph**, and **JSON-LD structured data** objects for
Laravel applications. Any Eloquent model can carry at most one metadata record, one Open Graph record, and one
JSON-LD record, rendered into `<head>` meta tags and `<script type="application/ld+json">` blocks.
Global/site-wide values are resolved through a pluggable `SettingStore` (auto-wired to
`oi-lab/oi-laravel-settings` when installed) with a config-default fallback.

## Core Concepts

- **Metadata** — one-per-parent polymorphic record holding `title`, `description`, `keywords[]`, `author`,
  `copyright`, `language` (ISO), `revisit_after`, `robots`, `googlebot`. Lives in the `metadata` table.
- **OpenGraph** — one-per-parent polymorphic record holding `type`, `title`, `description`, `url`, and an
  `image` object (`url`, `width`, `height`). Lives in the `open_graphs` table.
- **JsonLd** — one-per-parent polymorphic record holding a `graphs` list of Schema.org nodes; each graph is
  rendered as its own `<script type="application/ld+json">` block. Lives in the `json_ld` table.
- **One per parent** — all relations are `morphOne`, with a unique index on the morph columns. The single
  JSON-LD record still holds several graphs, so a page can expose multiple structured-data objects.
- **DTOs** — `MetadataData`, `OpenGraphData`, `OpenGraphImageData`, `JsonLdData` (spatie/laravel-data).
- **Schema builder** — `OiLab\OiLaravelMetadata\Support\Schema`, a fluent Schema.org node builder
  (`Schema::article()->headline(...)->author(Schema::person()->name(...))`); any method sets the matching
  property, nested nodes resolve recursively.
- **Services** — `MetaService`, `OgService`, and `JsonLdService` (facades `Meta`, `Og`, `JsonLd`) read, write,
  and render.
- **Blade directives** — `@meta`, `@og`, `@jsonLd`, each taking an optional source.
- **Shared subject** — `SeoContext` (facade `Seo`) holds the current SEO model so the directives render with no
  argument; falls back to the route-bound model.
- **Traits** — `HasMetadata`, `HasOpenGraph`, `HasJsonLd`, and the combined `HasMeta`.

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
$page->jsonLd;     // MorphOne — at most one JsonLd (holding a list of graphs)
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

## Writing JSON-LD Structured Data

Compose Schema.org nodes with the `Schema` builder and store them via `JsonLd::update()` (or `syncJsonLd()`).
Pass a `JsonLdData` (multiple graphs), a single `Schema`, or a raw array:

```php
use OiLab\OiLaravelMetadata\Data\JsonLdData;
use OiLab\OiLaravelMetadata\Facades\JsonLd;
use OiLab\OiLaravelMetadata\Support\Schema;

JsonLd::update($page, JsonLdData::make(
    Schema::article()
        ->headline('About us')
        ->datePublished('2026-07-07')
        ->author(Schema::person()->name('OI Lab'))
        ->publisher(Schema::organization()->name('Acme')),
    Schema::breadcrumbList()->itemListElement([
        Schema::listItem()->set('position', 1)->name('Home')->item('https://example.com'),
        Schema::listItem()->set('position', 2)->name('About')->item('https://example.com/about'),
    ]),
));

$page->syncJsonLd(Schema::webPage()->name('About us')); // single-graph shortcut
```

Named factories exist for common Google types (`article`, `newsArticle`, `blogPosting`, `webPage`, `webSite`,
`organization`, `person`, `imageObject`, `breadcrumbList`, `listItem`, `product`, `offer`, `faqPage`,
`question`, `answer`); use `Schema::type('Recipe')` for anything else. Use `->set('@id', ...)` / `->id(...)`
for `@`-prefixed keywords.

## Rendering `<head>` Tags

Use the Blade directives `@meta`, `@og`, `@jsonLd` (or the facade `render()` methods):

```blade
@meta($page)
@og($page)
@jsonLd($page)
```

`@meta` (or `Meta::render()`) emits `description`, `keywords`, `author`, `copyright`, `language`,
`revisit-after`, `robots`, `googlebot`, plus site verification tags from settings (`google-site-verification`,
`google`). `@og` (or `Og::render()`) emits `og:*` plus `og:locale`, `og:site_name` and `fb:app_id` resolved
from settings. `@jsonLd($source)` (or `JsonLd::render($source)`) emits one `<script type="application/ld+json">`
block per graph — `$source` may be a model, a `JsonLdData`, a `Schema`, or a raw array. JSON is hex-escaped so
it is safe inside `<script>`, and a top-level `@context` (`https://schema.org`) is injected when absent.

### Rendering without an argument

The three directives take an **optional** source. Called with no argument they render the shared SEO subject:

```php
use OiLab\OiLaravelMetadata\Facades\Seo;

Seo::for($page); // set once in a controller or view composer
```

```blade
@meta
@og
@jsonLd
```

When no subject is set explicitly, it is auto-resolved from the current route's model binding (the last bound
model exposing the relevant relation). An explicit argument (`@meta($other)`) always overrides. Disable with
`config('oi-laravel-metadata.auto_resolve_subject')`. The shared subject lives in the `SeoContext` singleton
(facade `Seo`).

## Setting Integration

Settings are read/written through a pluggable `OiLab\OiLaravelMetadata\Contracts\SettingStore`.
Resolution order: explicit `config('oi-laravel-metadata.settings.store')` class →
`oi-lab/oi-laravel-settings` adapter when installed (recommended, auto-wired, listed under
`suggest`) → generic key/value `Setting` model (`settings.model`) → config defaults.
`SettingResolver` / `SettingsInstaller` are thin façades over the resolved store.

Seed defaults with:

```bash
php artisan metadata:install-settings
```

Seeded keys (idempotent, existing keys untouched): `METADATA_FACEBOOK_APP_ID`,
`METADATA_GOOGLE_SITE_VERIFICATION`, `METADATA_GOOGLE_BOT`, `METADATA_GOOGLE`,
`METADATA_ROBOTS` (`index, follow`), `METADATA_OG_LOCALE` (`fr`), `METADATA_OG_SITE_NAME`,
`METADATA_OG_TYPE` (`website`). The generic-model class and key/value columns are configurable under
`config/oi-laravel-metadata.php` → `settings`. When no store is available, the resolver falls back to
the config defaults.

## Validation

- **Form requests**: `MetadataRequest`, `OpenGraphRequest`.
- **Rules**: `IsoLanguageRule` (e.g. `fr`, `en`, `fr-FR`), `RobotsRule` (e.g. `index, follow`).

## Conventions

- Always resolve model classes through `OiMetadata` (`OiMetadata::metadataModel()`,
  `OiMetadata::openGraphModel()`, `OiMetadata::jsonLdModel()`), never reference the concrete models directly.
- All three relations are `morphOne`; writes use `updateOrCreate` so a parent never gets a second record.
- `keywords`, `image`, and `graphs` are stored as JSON (array cast).
- The `@jsonLd` Blade directive is registered by the service provider; JSON-LD is encoded with
  `JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.

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
