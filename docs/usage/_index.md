---
title: Overview
description: How the traits, models, DTOs, and services fit together
section: usage
order: 1
---

# Usage Overview

The package revolves around two polymorphic models attached to a host model:

- **`Metadata`** — standard SEO meta tags, exposed via the `HasMetadata` trait's `metadata()` relation.
- **`OpenGraph`** — social sharing data, exposed via the `HasOpenGraph` trait's `openGraph()` relation.

Both are `morphOne` relations: a host model has **at most one** of each.

## Opt a model in

Add a trait to any Eloquent model:

```php
use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelMetadata\Concerns\HasMeta;

class Page extends Model
{
    use HasMeta; // both relations — or HasMetadata / HasOpenGraph individually
}
```

You now have the relations:

```php
$page->metadata;   // Metadata|null
$page->openGraph;  // OpenGraph|null
```

## The moving parts

| Piece | Role |
|-------|------|
| `HasMetadata` / `HasOpenGraph` / `HasMeta` | Traits that add the polymorphic relations and helpers |
| `MetadataData` / `OpenGraphData` / `OpenGraphImageData` | Typed DTOs (spatie/laravel-data) you read and write |
| `MetaService` / `OgService` | Services that read, write (`updateOrCreate`), and render |
| `Meta` / `Og` | Facades over the two services |
| `OiMetadata` | Resolver for the configurable model classes |

## Where to go next

- [Managing metadata](metadata.md) — write and read the SEO metadata object.
- [Managing Open Graph](open-graph.md) — write and read the Open Graph object.
- [Rendering tags](rendering.md) — output the `<head>` meta tags.
