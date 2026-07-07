<?php

use OiLab\OiLaravelMetadata\Models\JsonLd;
use OiLab\OiLaravelMetadata\Models\Metadata;
use OiLab\OiLaravelMetadata\Models\OpenGraph;

return [
    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | The model classes used by the package. Override these with your own
    | classes (extending the package base models) to customize behavior. Always
    | resolve them through the OiMetadata helper so overrides keep working.
    |
    */
    'models' => [
        'metadata' => Metadata::class,
        'open_graph' => OpenGraph::class,
        'json_ld' => JsonLd::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON-LD Structured Data
    |--------------------------------------------------------------------------
    |
    | Options for rendering JSON-LD `<script type="application/ld+json">` blocks.
    | `context` is the default `@context` injected into each top-level graph
    | when it does not define its own. Enable `pretty` to pretty-print the JSON
    | (useful while debugging; keep it compact in production).
    |
    */
    'json_ld' => [
        'context' => 'https://schema.org',
        'pretty' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic Subject Resolution
    |--------------------------------------------------------------------------
    |
    | When true, the `@meta`, `@og`, and `@jsonLd` Blade directives (called with
    | no argument) fall back to the last route-bound Eloquent model exposing the
    | relevant relation. Set a subject explicitly with `Seo::for($model)` to
    | override it, or disable this to require an explicit subject everywhere.
    |
    */
    'auto_resolve_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Metadata Values
    |--------------------------------------------------------------------------
    |
    | Fallback values used by MetaService when a model has no metadata record
    | or leaves a field empty. The `robots` and `googlebot` defaults are also
    | resolved from the host application Setting model when available.
    |
    */
    'defaults' => [
        'language' => 'fr',
        'robots' => 'index, follow',
        'revisit_after' => '7 days',
    ],

    /*
    |--------------------------------------------------------------------------
    | Setting Model Integration
    |--------------------------------------------------------------------------
    |
    | When the host application exposes a key/value Setting model, the package
    | can seed default metadata settings into it and resolve global values
    | (Open Graph locale, site name, Facebook App ID, verification tokens...)
    | from it. The installer and resolver no-op gracefully when the model is
    | absent, falling back to the `defaults` map below.
    |
    */
    'settings' => [
        // Explicit SettingStore implementation (class-string). Leave null to
        // auto-detect: the oi-lab/oi-laravel-settings adapter is used when that
        // package is installed, otherwise the generic key/value model below.
        'store' => env('OI_METADATA_SETTING_STORE'),

        'model' => 'App\\Models\\Setting',
        'key_column' => 'key',
        'value_column' => 'value',

        'defaults' => [
            'METADATA_FACEBOOK_APP_ID' => '',
            'METADATA_GOOGLE_SITE_VERIFICATION' => '',
            'METADATA_GOOGLE_BOT' => '',
            'METADATA_GOOGLE' => '',
            'METADATA_ROBOTS' => 'index, follow',
            'METADATA_OG_LOCALE' => 'fr',
            'METADATA_OG_SITE_NAME' => '',
            'METADATA_OG_TYPE' => 'website',
        ],
    ],
];
