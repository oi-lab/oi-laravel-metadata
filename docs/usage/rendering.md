---
title: Rendering Head Tags
description: Outputting the metadata and Open Graph tags into your layout
section: usage
order: 4
---

# Rendering Head Tags

The package renders escaped HTML `<meta>` tags and JSON-LD `<script>` blocks ready to drop into your `<head>`.
Empty values are omitted, and every meta value is passed through `e()`.

## Blade directives

The simplest way is the three Blade directives — `@meta`, `@og`, and `@jsonLd`:

```blade
<head>
    <title>{{ $page->metadata?->title ?? config('app.name') }}</title>

    @meta($page)
    @og($page)
    @jsonLd($page)
</head>
```

Each accepts a model, a matching DTO, or (for `@jsonLd`) a `Schema` builder or raw array.

### Rendering without passing `$page`

Set a shared **SEO subject** once and the directives render it with no argument. Set it in a controller
before returning the view, or in a view composer:

```php
use OiLab\OiLaravelMetadata\Facades\Seo;

public function show(Page $page)
{
    Seo::for($page);

    return view('pages.show', compact('page'));
}
```

```blade
<head>
    @meta
    @og
    @jsonLd
</head>
```

When no subject is set explicitly, it is **auto-resolved from the current route's model binding**: on a
`Route::get('/pages/{page}', ...)` route, the last bound model exposing the relevant relation is used — so the
directives work with zero setup. An explicit argument (`@meta($other)`) always overrides both. Disable
auto-resolution with `config('oi-laravel-metadata.auto_resolve_subject')`.

Each directive still renders site-wide values (verification tags, `og:locale`, …) even without a subject.

## From the facades

The facades return an `Illuminate\Support\HtmlString`, so `{!! !!}` outputs the markup without
double-escaping:

```blade
{!! Meta::render($page) !!}
{!! Og::render($page) !!}
```

See [Managing JSON-LD](json-ld.md) for the full structured-data workflow.

## What `Meta::render()` outputs

For a model's metadata, it emits the populated tags among:

```html
<meta name="description" content="Who we are">
<meta name="keywords" content="team, company">
<meta name="author" content="OI Lab">
<meta name="copyright" content="© Acme">
<meta name="language" content="fr">
<meta name="revisit-after" content="7 days">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
```

It also appends site-wide verification tags resolved from settings, when configured:

```html
<meta name="google-site-verification" content="...">
<meta name="google" content="...">
```

When a field is empty, sensible fallbacks apply: `language` and `revisit-after` fall back to
`config('oi-laravel-metadata.defaults')`, and `robots` / `googlebot` fall back to the `METADATA_ROBOTS` /
`METADATA_GOOGLE_BOT` settings.

## What `Og::render()` outputs

```html
<meta property="og:type" content="website">
<meta property="og:title" content="About us">
<meta property="og:description" content="Who we are">
<meta property="og:url" content="https://example.com/about">
<meta property="og:image" content="https://example.com/og/about.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="fr">
<meta property="og:site_name" content="Acme">
<meta property="fb:app_id" content="123456789">
```

The `og:locale`, `og:site_name`, and `fb:app_id` values come from the `METADATA_OG_LOCALE`,
`METADATA_OG_SITE_NAME`, and `METADATA_FACEBOOK_APP_ID` settings.

## Rendering from a DTO

You can render directly from a DTO instead of a model — handy for pages not backed by a model:

```php
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Facades\Meta;

$html = Meta::render(new MetadataData(
    title: 'Search results',
    robots: 'noindex, follow',
));
```

## Trait shortcuts

When a model uses the traits, you can render straight off the instance:

```php
$page->renderMetadata();  // string of <meta name="..."> tags
$page->renderOpenGraph(); // string of <meta property="og:..."> tags
$page->renderJsonLd();    // string of <script type="application/ld+json"> blocks
```
