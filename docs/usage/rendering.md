---
title: Rendering Head Tags
description: Outputting the metadata and Open Graph tags into your layout
section: usage
order: 4
---

# Rendering Head Tags

Both services render escaped HTML `<meta>` tags ready to drop into your `<head>`. Empty values are omitted, and
every value is passed through `e()`.

## In a Blade layout

```blade
<head>
    <title>{{ $page->metadata?->title ?? config('app.name') }}</title>

    {!! Meta::render($page) !!}
    {!! Og::render($page) !!}
    @jsonLd($page)
</head>
```

`Meta::render()` and `Og::render()` return an `Illuminate\Support\HtmlString`, so `{!! !!}` outputs the markup
without double-escaping. The `@jsonLd` directive renders JSON-LD `<script type="application/ld+json">` blocks —
see [Managing JSON-LD](json-ld.md) for the full structured-data workflow.

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
```
