---
title: Managing JSON-LD
description: Composing and rendering Schema.org structured data as JSON-LD
section: usage
order: 5
---

# Managing JSON-LD

The `JsonLd` object holds a list of [Schema.org](https://schema.org) graphs — each rendered as its own
`<script type="application/ld+json">` block. A single record can carry several graphs, so a page can expose an
`Article`, a `BreadcrumbList`, and an `Organization` at once, following the
[Google structured data guidelines](https://developers.google.com/search/docs/appearance/structured-data/article).

## Add the trait

```php
use OiLab\OiLaravelMetadata\Concerns\HasJsonLd;

class Page extends Model
{
    use HasJsonLd;
}
```

## The Schema builder

`OiLab\OiLaravelMetadata\Support\Schema` is a fluent builder for a single Schema.org node. Any method call sets
the matching schema.org property, and nested `Schema` nodes (or arrays of them) resolve recursively on render.

```php
use OiLab\OiLaravelMetadata\Support\Schema;

$article = Schema::article()
    ->headline('About us')
    ->datePublished('2026-07-07')
    ->dateModified('2026-07-08')
    ->image('https://example.com/og/about.png')
    ->author(Schema::person()->name('Jane Doe'))
    ->publisher(
        Schema::organization()
            ->name('Acme')
            ->logo(Schema::imageObject()->url('https://example.com/logo.png'))
    );
```

Named factories cover the common Google types — `article()`, `newsArticle()`, `blogPosting()`, `webPage()`,
`webSite()`, `organization()`, `person()`, `imageObject()`, `breadcrumbList()`, `listItem()`, `product()`,
`offer()`, `faqPage()`, `question()`, `answer()`. For any other type use `Schema::type('Recipe')`.

For `@`-prefixed keywords that cannot be method names, use `set()` or the `id()` helper:

```php
Schema::organization()
    ->id('https://example.com/#org')   // sets @id
    ->set('@context', 'https://schema.org');
```

## Writing structured data

`JsonLd::update()` accepts a `JsonLdData` (several graphs), a single `Schema`, or a raw array. Writes use
`updateOrCreate`, so a parent keeps a single record.

```php
use OiLab\OiLaravelMetadata\Data\JsonLdData;
use OiLab\OiLaravelMetadata\Facades\JsonLd;
use OiLab\OiLaravelMetadata\Support\Schema;

JsonLd::update($page, JsonLdData::make(
    Schema::article()->headline('About us')->author(Schema::person()->name('Jane Doe')),
    Schema::breadcrumbList()->itemListElement([
        Schema::listItem()->set('position', 1)->name('Home')->item('https://example.com'),
        Schema::listItem()->set('position', 2)->name('About')->item('https://example.com/about'),
    ]),
));
```

The trait helper does the same, and accepts a single builder for the common one-graph case:

```php
$page->syncJsonLd(Schema::webPage()->name('About us'));
```

## Rendering

Use the `@jsonLd` Blade directive — it accepts a model, a `JsonLdData`, a `Schema`, or a raw array:

```blade
<head>
    @jsonLd($page)
</head>
```

```php
JsonLd::render($page);                 // Illuminate\Support\HtmlString
$page->renderJsonLd();                 // string
```

Each graph becomes its own script block. A top-level `@context` (`https://schema.org` by default) is injected
when the graph does not declare one, and the JSON is encoded with `JSON_HEX_TAG | JSON_HEX_AMP` so `<`, `>`,
and `&` cannot break out of the `<script>` tag.

Called with no argument, `@jsonLd` renders the shared SEO subject (`Seo::for($model)`) or the current
route-bound model — see [Rendering tags](rendering.md#rendering-without-passing-page).

Render ad-hoc, non-persisted structured data straight from a builder:

```blade
@jsonLd(\OiLab\OiLaravelMetadata\Support\Schema::webSite()->name(config('app.name'))->url(url('/')))
```

## Reading structured data

```php
$page->jsonLd;                 // JsonLd|null
$page->jsonLd?->graphs;        // list<array>|null — cast from JSON

$data = JsonLd::toData($page); // JsonLdData
$data->graphs;                 // list<array<string, mixed>>
```

## Configuration & storage notes

- `graphs` is stored as a JSON column and cast to an array.
- The relation is `morphOne`; the underlying table is `json_ld` with a unique index on
  `(metable_type, metable_id)`.
- `config('oi-laravel-metadata.json_ld.context')` sets the injected `@context` (default `https://schema.org`).
- Set `config('oi-laravel-metadata.json_ld.pretty')` to `true` to pretty-print the JSON while debugging.
