---
title: Validation
description: Form requests and the ISO language and robots rules
section: advanced
order: 2
---

# Validation

The package ships ready-made form requests and two reusable validation rules.

## Form requests

`MetadataRequest` and `OpenGraphRequest` validate incoming payloads against the DTO shapes. Type-hint them in a
controller and pass the validated data straight to the services:

```php
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Http\Requests\MetadataRequest;
use OiLab\OiLaravelMetadata\Facades\Meta;

public function update(MetadataRequest $request, Page $page)
{
    Meta::update($page, MetadataData::from($request->validated()));

    return back();
}
```

`MetadataRequest` rules cover `title`, `description`, `keywords` (array of strings), `author`, `copyright`,
`language` (via `IsoLanguageRule`), `revisit_after`, `robots` and `googlebot` (via `RobotsRule`).

`OpenGraphRequest` rules cover `type`, `title`, `description`, `url`, and the nested `image.url` / `image.width`
/ `image.height`.

## The `IsoLanguageRule`

Validates that a value is a plausible ISO 639-1 language code, optionally with an ISO 3166-1 region suffix.

```php
use OiLab\OiLaravelMetadata\Rules\IsoLanguageRule;

$request->validate([
    'language' => ['required', new IsoLanguageRule],
]);
```

| Accepted | Rejected |
|----------|----------|
| `fr`, `en`, `pt`  | `french`, `F` |
| `fr-FR`, `pt_BR`  | `fr-france`, `fr-`, `123` |

## The `RobotsRule`

Validates a comma-separated list of recognised robots directives — including parameterized ones like
`max-snippet:-1`.

```php
use OiLab\OiLaravelMetadata\Rules\RobotsRule;

$request->validate([
    'robots' => ['required', new RobotsRule],
]);
```

| Accepted | Rejected |
|----------|----------|
| `index, follow` | `index, banana` |
| `noindex, nofollow` | `crawl` |
| `all`, `none` | `follow, nope` |
| `max-snippet:-1, max-image-preview:large` | |

Recognised bare directives: `index`, `noindex`, `follow`, `nofollow`, `all`, `none`, `noarchive`, `nosnippet`,
`noimageindex`, `notranslate`, `nocache`. Recognised parameterized directives: `max-snippet`,
`max-image-preview`, `max-video-preview`, `unavailable_after`.

Both rules are standalone — use them anywhere, not just in the bundled form requests.
