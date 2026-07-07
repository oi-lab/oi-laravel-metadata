# Laravel Metadata

Use the `oi-laravel-metadata` package to attach polymorphic SEO metadata, Open Graph, and JSON-LD structured
data to any Eloquent model. Add the `HasMetadata`, `HasOpenGraph`, `HasJsonLd`, or combined `HasMeta` trait —
each parent has at most one `metadata`, `openGraph`, and `jsonLd` record (`morphOne`). Read/write through the
`MetaService`/`OgService`/`JsonLdService` (or the `Meta`/`Og`/`JsonLd` facades) using the `MetadataData` /
`OpenGraphData` / `JsonLdData` spatie/laravel-data DTOs, and render head tags with `Meta::render($model)` /
`Og::render($model)` / `@jsonLd($model)`. Compose JSON-LD with the fluent `Schema` builder
(`Schema::article()->headline(...)->author(Schema::person()->name(...))`). Resolve model classes through
`OiMetadata` so config overrides apply. Global values (Facebook App ID, Google verification, OG
locale/site name/type, robots) are read from the host application `Setting` model when present, seeded via
`php artisan metadata:install-settings`.

- IMPORTANT: Activate `oilab-laravel-metadata` when working with SEO meta tags, Open Graph, JSON-LD /
  Schema.org structured data, social sharing previews, or per-model `<head>` metadata in this Laravel
  application.
