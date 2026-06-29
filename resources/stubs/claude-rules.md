# Laravel Metadata

Use the `oi-laravel-metadata` package to attach polymorphic SEO metadata and Open Graph data to any Eloquent
model. Add the `HasMetadata`, `HasOpenGraph`, or combined `HasMeta` trait — each parent has at most one
`metadata` and one `openGraph` record (`morphOne`). Read/write through the `MetaService`/`OgService` (or the
`Meta`/`Og` facades) using the `MetadataData` / `OpenGraphData` spatie/laravel-data DTOs, and render head tags
with `Meta::render($model)` / `Og::render($model)`. Resolve model classes through `OiMetadata` so config
overrides apply. Global values (Facebook App ID, Google verification, OG locale/site name/type, robots) are
read from the host application `Setting` model when present, seeded via `php artisan metadata:install-settings`.

- IMPORTANT: Activate `oilab-laravel-metadata` when working with SEO meta tags, Open Graph, social sharing
  previews, or per-model `<head>` metadata in this Laravel application.
