<?php

namespace OiLab\OiLaravelMetadata\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OiLab\OiLaravelMetadata\Data\MetadataData;
use OiLab\OiLaravelMetadata\Facades\Meta;
use OiLab\OiLaravelMetadata\OiMetadata;

/**
 * HasMetadata Trait
 *
 * Gives a model a single polymorphic metadata record and convenience helpers
 * to read, write, and render it.
 */
trait HasMetadata
{
    /**
     * Get the metadata record attached to this model.
     *
     * @return MorphOne<Model, $this>
     */
    public function metadata(): MorphOne
    {
        return $this->morphOne(OiMetadata::metadataModel(), 'metable');
    }

    /**
     * Create or update the metadata record from a data object.
     */
    public function syncMetadata(MetadataData $data): Model
    {
        return Meta::update($this, $data);
    }

    /**
     * Render this model's metadata as HTML meta tags.
     */
    public function renderMetadata(): string
    {
        return Meta::render($this)->toHtml();
    }
}
