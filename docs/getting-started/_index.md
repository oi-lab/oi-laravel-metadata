---
title: Introduction
description: Discover OI Laravel Metadata and what it can do for your project
section: getting-started
order: 1
---

# OI Laravel Metadata

OI Laravel Metadata attaches **SEO metadata** and **Open Graph** data to any Eloquent model. Instead of
scattering meta-tag logic across controllers and Blade views, you add a trait to a model and gain two
polymorphic records — one `metadata` and one `openGraph` — that you read, write, and render through dedicated
services.

## Why use this package?

Most applications eventually need per-page `<head>` metadata: a tailored `title`, `description`, social
sharing previews, robots directives. Building that by hand means a bespoke table (or a pile of nullable
columns) on every model, plus repeated tag-rendering code. This package centralizes all of it:

- One trait — `HasMetadata`, `HasOpenGraph`, or the combined `HasMeta` — makes **any** model meta-aware.
- Each model has **at most one** metadata record and one Open Graph record (`morphOne`, enforced by a unique
  index).
- Typed **DTOs** (spatie/laravel-data) describe the objects you pass in and get back.
- The `MetaService` / `OgService` services (and `Meta` / `Og` facades) **render escaped `<head>` tags**.
- Site-wide values (Facebook App ID, Google verification, Open Graph locale...) are resolved from a host
  application **`Setting` model** when present.

## The two objects

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
