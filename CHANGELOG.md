# Changelog

All notable changes to `oi-laravel-metadata` will be documented in this file.

## 1.2.0 - 2026-07-07

### Added

- `@meta` and `@og` Blade directives, mirroring `@jsonLd`, each taking an optional source.
- Shared SEO subject via `SeoContext` (facade `Seo`): set it once with `Seo::for($model)` and the three
  directives render it with no argument; falls back to the current route's bound model.
- `auto_resolve_subject` config flag (default `true`) toggling the route-model fallback.

## 1.1.0 - 2026-07-07

### Added

- Polymorphic `JsonLd` model (one record per parent, `morphOne`) holding a list of Schema.org graphs, backed by
  the `json_ld` table.
- `HasJsonLd` trait (also folded into the combined `HasMeta` trait) with `jsonLd()`, `syncJsonLd()`, and
  `renderJsonLd()` helpers.
- Fluent `Schema` builder (`OiLab\OiLaravelMetadata\Support\Schema`) for composing Schema.org nodes, with named
  factories for common Google types.
- `JsonLdData` DTO and `JsonLdService` with the `JsonLd` facade for reading, writing, and rendering structured
  data.
- `@jsonLd` Blade directive rendering one `<script type="application/ld+json">` block per graph, with a
  safely-injected `@context` and script-safe JSON encoding.
- `json_ld` configuration section (`context`, `pretty`).

## 1.0.0 - 2026-06-29

### Added

- Polymorphic `Metadata` and `OpenGraph` models (one record per parent, `morphOne`).
- `HasMetadata`, `HasOpenGraph`, and combined `HasMeta` traits.
- `MetaService` / `OgService` with `Meta` / `Og` facades for reading, writing, and rendering head tags.
- `MetadataData`, `OpenGraphData`, `OpenGraphImageData` DTOs (spatie/laravel-data).
- `IsoLanguageRule` and `RobotsRule` validation rules, plus `MetadataRequest` / `OpenGraphRequest` form requests.
- Host application `Setting` model integration: `SettingResolver`, `SettingsInstaller`, and the
  `metadata:install-settings` artisan command seeding default metadata settings.
- Publishable config and migrations, and the `oilab-laravel-metadata` AI assistant skill.
