<?php

namespace OiLab\OiLaravelMetadata;

/**
 * OiMetadata
 *
 * Central resolver for the configurable model classes used across the package.
 * Package internals (models, traits, factories, services) resolve their
 * collaborators through these helpers so host applications can swap in their
 * own classes via config.
 */
class OiMetadata
{
    /**
     * Resolve the configured Metadata model class.
     *
     * @return class-string
     */
    public static function metadataModel(): string
    {
        return config('oi-laravel-metadata.models.metadata', Models\Metadata::class);
    }

    /**
     * Resolve the configured OpenGraph model class.
     *
     * @return class-string
     */
    public static function openGraphModel(): string
    {
        return config('oi-laravel-metadata.models.open_graph', Models\OpenGraph::class);
    }

    /**
     * Resolve the configured JsonLd model class.
     *
     * @return class-string
     */
    public static function jsonLdModel(): string
    {
        return config('oi-laravel-metadata.models.json_ld', Models\JsonLd::class);
    }
}
