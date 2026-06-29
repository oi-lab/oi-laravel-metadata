---
title: Managing Metadata
description: Writing and reading the SEO metadata object on a model
section: usage
order: 2
---

# Managing Metadata

The `Metadata` object holds standard SEO meta fields: `title`, `description`, `keywords[]`, `author`,
`copyright`, `language`, `revisit_after`, `robots`, and `googlebot`.

## Add the trait

```php
use OiLab\OiLaravelMetadata\Concerns\HasMetadata;

class Page extends Model
{
    use HasMetadata;
}
```

## Writing metadata

Build a `MetadataData` DTO and pass it to the service (or the trait helper). Writes use `updateOrCreate`, so a
parent never ends up with a second record — calling it again updates the existing one.

```php
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Facades\Meta;

Meta::update($page, new MetadataData(
    title: 'About us',
    description: 'Who we are and what we build',
    keywords: ['team', 'company', 'laravel'],
    author: 'OI Lab',
    copyright: '© Acme',
    language: 'fr',
    revisit_after: '7 days',
    robots: 'index, follow',
    googlebot: 'index, follow',
));
```

The trait exposes the same write through a helper:

```php
$page->syncMetadata(new MetadataData(title: 'About us'));
```

### From request input

`MetadataData` hydrates straight from validated request data:

```php
use OiLab\OiLaravelMetadata\Http\Requests\MetadataRequest;

public function update(MetadataRequest $request, Page $page)
{
    Meta::update($page, MetadataData::from($request->validated()));

    return back();
}
```

See [Validation](../advanced/validation.md) for the form request and rules.

## Reading metadata

The relation returns the Eloquent model:

```php
$page->metadata;            // Metadata|null
$page->metadata?->keywords; // array — cast from JSON
```

To get a typed DTO instead, use the service:

```php
$data = Meta::toData($page); // MetadataData (empty DTO when no record exists)

$data->title;
$data->keywords; // string[]
```

## Storage notes

- `keywords` is stored as a JSON column and cast to an array.
- The relation is `morphOne`; the underlying table is `metadata` with a unique index on
  `(metable_type, metable_id)`.
