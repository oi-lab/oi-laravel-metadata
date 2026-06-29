<?php

namespace OiLab\OiLaravelMetadata\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OiLab\OiLaravelMetadata\Data\OpenGraphData;
use OiLab\OiLaravelMetadata\Facades\Og;
use OiLab\OiLaravelMetadata\OiMetadata;

/**
 * HasOpenGraph Trait
 *
 * Gives a model a single polymorphic Open Graph record and convenience helpers
 * to read, write, and render it.
 */
trait HasOpenGraph
{
    /**
     * Get the Open Graph record attached to this model.
     *
     * @return MorphOne<Model, $this>
     */
    public function openGraph(): MorphOne
    {
        return $this->morphOne(OiMetadata::openGraphModel(), 'metable');
    }

    /**
     * Create or update the Open Graph record from a data object.
     */
    public function syncOpenGraph(OpenGraphData $data): Model
    {
        return Og::update($this, $data);
    }

    /**
     * Render this model's Open Graph data as HTML meta tags.
     */
    public function renderOpenGraph(): string
    {
        return Og::render($this)->toHtml();
    }
}
