---
title: Introduction
description: Discover OI Laravel Metadata and what it can do for your project
section: getting-started
order: 1
---

# OI Laravel Metadata

OI Laravel Metadata attaches **SEO metadata**, **Open Graph**, and **JSON-LD structured data** to any Eloquent
model. Instead of scattering meta-tag logic across controllers and Blade views, you add a trait to a model and
gain three polymorphic records — one `metadata`, one `openGraph`, and one `jsonLd` — that you read, write, and
render through dedicated services.

## Why use this package?

Most applications eventually need per-page `<head>` metadata: a tailored `title`, `description`, social
sharing previews, robots directives. Building that by hand means a bespoke table (or a pile of nullable
columns) on every model, plus repeated tag-rendering code. This package centralizes all of it:

- One trait — `HasMetadata`, `HasOpenGraph`, `HasJsonLd`, or the combined `HasMeta` — makes **any** model meta-aware.
- Each model has **at most one** metadata, Open Graph, and JSON-LD record (`morphOne`, enforced by a unique
  index).
- Typed **DTOs** (spatie/laravel-data) plus a fluent `Schema` builder describe the objects you pass in and get back.
- The `MetaService` / `OgService` / `JsonLdService` services (and `Meta` / `Og` / `JsonLd` facades) **render
  escaped `<head>` tags** and JSON-LD `<script>` blocks (via the `@jsonLd` Blade directive).
- Site-wide values (Facebook App ID, Google verification, Open Graph locale...) are resolved from a host
  application **`Setting` model** when present.

## The objects

A **Metadata** object describes standard SEO meta tags:

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

An **Open Graph** object describes the social sharing representation:

| Field | Type |
|-------|------|
| `type` | string |
| `title` | string |
| `description` | string |
| `url` | string |
| `image` | object (`url`, `width`, `height`) |

A **JSON-LD** object holds a list of Schema.org graphs, composed with the fluent `Schema` builder and rendered
as `<script type="application/ld+json">` blocks:

| Field | Type |
|-------|------|
| `graphs` | array of Schema.org nodes (`Article`, `BreadcrumbList`, `Organization`, …) |

## What it looks like

```php
use OiLab\OiLaravelMetadata\Concerns\HasMeta;

class Page extends Model
{
    use HasMeta;
}

$page->syncMetadata(new MetadataData(
    title: 'About us',
    description: 'Who we are',
    keywords: ['team', 'company'],
));

echo $page->renderMetadata(); // <meta> tags for the <head>
```

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- `spatie/laravel-data` ^4.0

## Next steps

Follow the [Installation](installation.md) guide to add the package to your project, then head to
[Usage](../usage/_index.md).
