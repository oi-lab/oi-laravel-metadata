---
title: Custom Models
description: Overriding the Metadata and OpenGraph models
section: advanced
order: 3
---

# Custom Models

Every model the package uses is resolved through the `OiMetadata` resolver, never referenced directly. This
lets host applications swap in their own subclasses from config.

## Overriding the Metadata model

Extend the base model and register it:

```php
namespace App\Models;

use OiLab\OiLaravelMetadata\Models\Metadata as BaseMetadata;

class Metadata extends BaseMetadata
{
    // your customizations
}
```

```php
// config/oi-laravel-metadata.php
'models' => [
    'metadata' => App\Models\Metadata::class,
],
```

The `HasMetadata` trait, the factory, and all internals now resolve your subclass via
`OiMetadata::metadataModel()`.

## Overriding the OpenGraph model

```php
namespace App\Models;

use OiLab\OiLaravelMetadata\Models\OpenGraph as BaseOpenGraph;

class OpenGraph extends BaseOpenGraph
{
    // your customizations
}
```

```php
// config/oi-laravel-metadata.php
'models' => [
    'open_graph' => App\Models\OpenGraph::class,
],
```

## Overriding the JsonLd model

```php
namespace App\Models;

use OiLab\OiLaravelMetadata\Models\JsonLd as BaseJsonLd;

class JsonLd extends BaseJsonLd
{
    // your customizations
}
```

```php
// config/oi-laravel-metadata.php
'models' => [
    'json_ld' => App\Models\JsonLd::class,
],
```

## The resolver

```php
use OiLab\OiLaravelMetadata\OiMetadata;

OiMetadata::metadataModel();  // configured Metadata class
OiMetadata::openGraphModel(); // configured OpenGraph class
OiMetadata::jsonLdModel();    // configured JsonLd class
```

Always go through these helpers in your own code instead of referencing the concrete model classes directly, so
config overrides keep working.
