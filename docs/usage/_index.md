---
title: Overview
description: How the traits, models, DTOs, and services fit together
section: usage
order: 1
---

# Usage Overview

The package revolves around three polymorphic models attached to a host model:

- **`Metadata`** — standard SEO meta tags, exposed via the `HasMetadata` trait's `metadata()` relation.
- **`OpenGraph`** — social sharing data, exposed via the `HasOpenGraph` trait's `openGraph()` relation.
- **`JsonLd`** — Schema.org structured data, exposed via the `HasJsonLd` trait's `jsonLd()` relation.

All are `morphOne` relations: a host model has **at most one** of each. The single JSON-LD record holds a list
of graphs, so a page can still expose several structured-data objects.

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
$page->jsonLd;     // JsonLd|null
```

## The moving parts

| Piece | Role |
|-------|------|
| `HasMetadata` / `HasOpenGraph` / `HasJsonLd` / `HasMeta` | Traits that add the polymorphic relations and helpers |
| `MetadataData` / `OpenGraphData` / `OpenGraphImageData` / `JsonLdData` | Typed DTOs (spatie/laravel-data) you read and write |
| `Schema` | Fluent Schema.org node builder for JSON-LD |
| `MetaService` / `OgService` / `JsonLdService` | Services that read, write (`updateOrCreate`), and render |
| `Meta` / `Og` / `JsonLd` | Facades over the three services |
| `OiMetadata` | Resolver for the configurable model classes |

## Where to go next

- [Managing metadata](metadata.md) — write and read the SEO metadata object.
- [Managing Open Graph](open-graph.md) — write and read the Open Graph object.
- [Managing JSON-LD](json-ld.md) — compose and render Schema.org structured data.
- [Rendering tags](rendering.md) — output the `<head>` meta tags.
