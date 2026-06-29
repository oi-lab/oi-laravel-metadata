<?php

namespace OiLab\OiLaravelMetadata\Concerns;

/**
 * HasMeta Trait
 *
 * Convenience trait combining both HasMetadata and HasOpenGraph for models that
 * need the full set of SEO metadata and Open Graph relationships.
 */
trait HasMeta
{
    use HasMetadata;
    use HasOpenGraph;
}
