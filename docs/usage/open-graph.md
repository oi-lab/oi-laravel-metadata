---
title: Managing Open Graph
description: Writing and reading the Open Graph object, including the image
section: usage
order: 3
---

# Managing Open Graph

The `OpenGraph` object holds the social sharing representation: `type`, `title`, `description`, `url`, and a
nested `image` object (`url`, `width`, `height`).

## Add the trait

```php
use OiLab\OiLaravelMetadata\Concerns\HasOpenGraph;

class Page extends Model
{
    use HasOpenGraph;
}
```

## Writing Open Graph data

Build an `OpenGraphData` DTO — the image is its own `OpenGraphImageData` DTO. Writes use `updateOrCreate`, so a
parent keeps a single record.

```php
use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Data\OpenGraphImageData;
use OiLab\OiLaravelMetadata\Facades\Og;

Og::update($page, new OpenGraphData(
    type: 'website',
    title: 'About us',
    description: 'Who we are',
    url: 'https://example.com/about',
    image: new OpenGraphImageData(
        url: 'https://example.com/og/about.png',
        width: 1200,
        height: 630,
    ),
));
```

The trait helper does the same:

```php
$page->syncOpenGraph(new OpenGraphData(type: 'article'));
```

If you omit `type`, the service falls back to the `METADATA_OG_TYPE` setting (default `website`) — see
[Setting Integration](../advanced/settings-integration.md).

### From request input

```php
use OiLab\OiLaravelMetadata\Http\Requests\OpenGraphRequest;

public function update(OpenGraphRequest $request, Page $page)
{
    Og::update($page, OpenGraphData::from($request->validated()));

    return back();
}
```

## Reading Open Graph data

```php
$page->openGraph;              // OpenGraph|null
$page->openGraph?->image;      // array{url, width, height}|null — cast from JSON

$data = Og::toData($page);     // OpenGraphData (with a nested OpenGraphImageData)
$data->image?->url;
$data->image?->width;
```

## Storage notes

- `image` is stored as a JSON column and rehydrated into an `OpenGraphImageData` DTO.
- The relation is `morphOne`; the underlying table is `open_graphs` with a unique index on
  `(metable_type, metable_id)`.
