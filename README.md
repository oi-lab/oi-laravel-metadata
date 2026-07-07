<img src="./assets/github-preview.png" alt="OI Laravel TypeScript Generator" width="100%" />

# OI Laravel Metadata

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oi-lab/oi-laravel-metadata.svg)](https://packagist.org/packages/oi-lab/oi-laravel-metadata)
[![Total Downloads](https://img.shields.io/packagist/dt/oi-lab/oi-laravel-metadata.svg)](https://packagist.org/packages/oi-lab/oi-laravel-metadata)
[![Tests](https://img.shields.io/github/actions/workflow/status/oi-lab/oi-laravel-metadata/tests.yml?label=tests)](https://github.com/oi-lab/oi-laravel-metadata/actions)
[![License](https://img.shields.io/github/license/oi-lab/oi-laravel-metadata)](LICENSE)

A Laravel package to attach polymorphic **SEO metadata**, **Open Graph**, and **JSON-LD structured data** to
**any** Eloquent model — with spatie/laravel-data DTOs, a fluent Schema.org builder, dedicated services,
validation rules, head-tag rendering, a `@jsonLd` Blade directive, and pluggable settings integration
(auto-wired to `oi-lab/oi-laravel-settings`).

## Features

- **Polymorphic Metadata, Open Graph & JSON-LD**: a single `metadata`, `openGraph`, and `jsonLd` record per model (`morphOne`)
- **Traits**: opt in with `HasMetadata`, `HasOpenGraph`, `HasJsonLd`, or the combined `HasMeta`
- **DTOs**: typed `MetadataData`, `OpenGraphData`, `OpenGraphImageData`, `JsonLdData` (spatie/laravel-data)
- **Fluent Schema.org builder**: compose any JSON-LD node with `Schema::article()->headline(...)->author(...)`
- **Services & Facades**: `MetaService` / `OgService` / `JsonLdService` (`Meta` / `Og` / `JsonLd`) to read, write, and render
- **Head-tag Rendering**: `Meta::render($model)` and `Og::render($model)` emit escaped `<meta>` tags
- **JSON-LD Rendering**: `JsonLd::render($model)` and the `@jsonLd($model)` Blade directive emit `<script type="application/ld+json">` blocks
- **Validation**: `MetadataRequest` / `OpenGraphRequest` form requests, plus `IsoLanguageRule` & `RobotsRule`
- **Setting Integration**: seeds and resolves site-wide values through a pluggable `SettingStore` (auto-wired to `oi-lab/oi-laravel-settings`, config-default fallback)

## The Objects

A **Metadata** object:

| Field | Type |
|-------|------|
| `title` | string |
| `description` | string |
| `keywords` | string[] |
| `author` | string |
| `copyright` | string |
| `language` | ISO code (e.g. `fr`, `en`) |
| `revisit_after` | string |
| `robots` | string |
| `googlebot` | string |

An **Open Graph** object:

| Field | Type |
|-------|------|
| `type` | string |
| `title` | string |
| `description` | string |
| `url` | string |
| `image` | object (`url`, `width`, `height`) |

A **JSON-LD** object holds a list of Schema.org graphs, each rendered as its own
`<script type="application/ld+json">` block:

| Field | Type |
|-------|------|
| `graphs` | array of Schema.org nodes (`Article`, `BreadcrumbList`, `Organization`, …) |

All three are polymorphic, with **at most one per parent** (enforced by a unique index on the morph columns).
The single JSON-LD record can still hold several graphs, so a page can expose multiple structured-data objects.

## Requirements

- PHP 8.2+
- Laravel 11.0+, 12.0+, or 13.0+
- [`spatie/laravel-data`](https://github.com/spatie/laravel-data) ^4.0

## Installation

```bash
composer require oi-lab/oi-laravel-metadata
```

The package auto-discovers its service provider. Publish and migrate:

```bash
php artisan vendor:publish --tag=oi-laravel-metadata-migrations
php artisan vendor:publish --tag=oi-laravel-metadata-config
php artisan migrate
```

This creates the `metadata`, `open_graphs`, and `json_ld` tables.

### Local Development

Inside the monorepo, add a path repository to your main project's `composer.json`:

```json
{
    "repositories": [
        { "type": "path", "url": "./packages/oi-lab/oi-laravel-metadata" }
    ]
}
```

## Usage

### Make a Model Meta-aware

```php
use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelMetadata\Concerns\HasMeta;

class Page extends Model
{
    use HasMeta; // or HasMetadata / HasOpenGraph individually
}
```

```php
$page->metadata;   // MorphOne — Metadata|null
$page->openGraph;  // MorphOne — OpenGraph|null
$page->jsonLd;     // MorphOne — JsonLd|null
```

### Write Values

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
    author: 'OI Lab',
    language: 'fr',
    robots: 'index, follow',
));

Og::update($page, new OpenGraphData(
    type: 'website',
    title: 'About us',
    url: 'https://example.com/about',
    image: new OpenGraphImageData('https://example.com/og.png', 1200, 630),
));
```

The trait helpers `$page->syncMetadata(...)` and `$page->syncOpenGraph(...)` do the same. Writes use
`updateOrCreate`, so a parent never ends up with a second record.

### Render `<head>` Tags

```blade
<head>
    {!! Meta::render($page) !!}
    {!! Og::render($page) !!}
</head>
```

`Meta::render()` outputs `description`, `keywords`, `author`, `copyright`, `language`, `revisit-after`,
`robots`, and `googlebot` tags, plus `google-site-verification` / `google` verification tags resolved from
settings. `Og::render()` outputs the `og:*` tags plus `og:locale`, `og:site_name`, and `fb:app_id` from
settings. Empty values are omitted; all values are HTML-escaped.

### JSON-LD Structured Data

Attach [Schema.org](https://schema.org) structured data (per the
[Google structured data guidelines](https://developers.google.com/search/docs/appearance/structured-data/article))
with the fluent `Schema` builder — any method call sets the matching schema.org property, and nested nodes are
resolved recursively:

```php
use OiLab\OiLaravelMetadata\Data\JsonLdData;
use OiLab\OiLaravelMetadata\Facades\JsonLd;
use OiLab\OiLaravelMetadata\Support\Schema;

JsonLd::update($page, JsonLdData::make(
    Schema::article()
        ->headline('About us')
        ->datePublished('2026-07-07')
        ->image('https://example.com/og.png')
        ->author(Schema::person()->name('OI Lab'))
        ->publisher(Schema::organization()->name('Acme')),
    Schema::breadcrumbList()->itemListElement([
        Schema::listItem()->set('position', 1)->name('Home')->item('https://example.com'),
        Schema::listItem()->set('position', 2)->name('About')->item('https://example.com/about'),
    ]),
));
```

`JsonLd::update()` accepts a `JsonLdData` object, a single `Schema` builder, or raw arrays. The single record
holds a **list of graphs**, so a page can expose several structured-data objects at once. The trait helper
`$page->syncJsonLd(...)` does the same.

Render one `<script type="application/ld+json">` block per graph with the `@jsonLd` Blade directive (or
`JsonLd::render()`):

```blade
<head>
    {!! Meta::render($page) !!}
    {!! Og::render($page) !!}
    @jsonLd($page)
</head>
```

`@jsonLd` accepts a model, a `JsonLdData` object, a `Schema` builder, or a raw array — handy for ad-hoc,
non-persisted structured data:

```blade
@jsonLd(\OiLab\OiLaravelMetadata\Support\Schema::webSite()->name(config('app.name'))->url(url('/')))
```

The rendered JSON is encoded with flags that keep it safe inside a `<script>` tag (`<`, `>`, and `&` are
hex-escaped), and a top-level `@context` (`https://schema.org` by default) is injected when the graph does not
declare its own. Enable `json_ld.pretty` in the config to pretty-print while debugging.

### Validation

```php
use OiLab\OiLaravelMetadata\Http\Requests\MetadataRequest;
use OiLab\OiLaravelMetadata\Http\Requests\OpenGraphRequest;

public function update(MetadataRequest $request, Page $page)
{
    Meta::update($page, MetadataData::from($request->validated()));
}
```

The `IsoLanguageRule` and `RobotsRule` rules are reusable on their own.

## Setting Integration

Seed the package defaults into your settings backend:

```bash
php artisan metadata:install-settings
```

Settings are read and written through a pluggable `SettingStore`. Install the
recommended [`oi-lab/oi-laravel-settings`](https://packagist.org/packages/oi-lab/oi-laravel-settings)
(listed under `suggest`) and it is wired automatically. Without it, the package
falls back to a generic key/value `Setting` model or no-ops gracefully.

This inserts the following keys (idempotently — existing keys are never overwritten):

| Key | Default |
|-----|---------|
| `METADATA_FACEBOOK_APP_ID` | `""` |
| `METADATA_GOOGLE_SITE_VERIFICATION` | `""` |
| `METADATA_GOOGLE_BOT` | `""` |
| `METADATA_GOOGLE` | `""` |
| `METADATA_ROBOTS` | `index, follow` |
| `METADATA_OG_LOCALE` | `fr` |
| `METADATA_OG_SITE_NAME` | `""` |
| `METADATA_OG_TYPE` | `website` |

Configure the backend in `config/oi-laravel-metadata.php`. Leave `store` null to auto-detect; the
`model` / column keys drive the generic key/value fallback:

```php
'settings' => [
    'store' => null, // class-string to force a specific SettingStore, else auto-detect
    'model' => App\Models\Setting::class,
    'key_column' => 'key',
    'value_column' => 'value',
    'defaults' => [ /* ... */ ],
],
```

When no store is present, the resolver and installer no-op gracefully and fall back to config
defaults. See [Advanced → Setting Integration](docs/advanced/settings-integration.md).

## Overriding Models

Resolve models through `OiMetadata` so your overrides apply everywhere:

```php
// config/oi-laravel-metadata.php
'models' => [
    'metadata' => App\Models\Metadata::class,   // extends OiLab\OiLaravelMetadata\Models\Metadata
    'open_graph' => App\Models\OpenGraph::class, // extends OiLab\OiLaravelMetadata\Models\OpenGraph
    'json_ld' => App\Models\JsonLd::class,       // extends OiLab\OiLaravelMetadata\Models\JsonLd
],
```

## AI Assistant Skills

```bash
php artisan oi:skills
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see the [License File](LICENSE) for more information.

## Credits

**[Olivier Lacombe](https://www.olacombe.com)** - Creator and maintainer

Olivier is a Product & Technology Director based in Montpellier, France, with over 20 years of experience innovating in UX/UI and emerging technologies. He specializes in guiding enterprises toward cutting-edge digital solutions, combining user-centered design with continuous optimization and artificial intelligence integration.

**Projects & Resources:**
- [OI Dev Docs](https://dev.olacombe.com) - Documentation for all Open Source OI Lab packages
- [OnAI](https://onai.olacombe.com) - Training courses and masterclasses on generative AI for businesses
- [Promptr](https://promptr.olacombe.com) - Prompt engineering Management Platform

## Support

For support, please open an issue on the [GitHub repository](https://github.com/oi-lab/oi-laravel-metadata/issues).
