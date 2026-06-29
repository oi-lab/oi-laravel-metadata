# Changelog

All notable changes to `oi-laravel-metadata` will be documented in this file.

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
