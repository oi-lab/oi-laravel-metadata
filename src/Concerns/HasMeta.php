<?php

namespace OiLab\OiLaravelMetadata\Concerns;

/**
 * HasMeta Trait
 *
 * Convenience trait combining HasMetadata, HasOpenGraph, and HasJsonLd for
 * models that need the full set of SEO metadata, Open Graph, and JSON-LD
 * structured data relationships.
 */
trait HasMeta
{
    use HasJsonLd;
    use HasMetadata;
    use HasOpenGraph;
}
