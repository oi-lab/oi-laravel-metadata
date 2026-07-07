<?php

namespace OiLab\OiLaravelMetadata\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OiLab\OiLaravelMetadata\Data\JsonLdData;
use OiLab\OiLaravelMetadata\Facades\JsonLd as JsonLdFacade;
use OiLab\OiLaravelMetadata\OiMetadata;
use OiLab\OiLaravelMetadata\Support\Schema;

/**
 * HasJsonLd Trait
 *
 * Gives a model a single polymorphic JSON-LD record and convenience helpers to
 * read, write, and render its Schema.org structured data.
 */
trait HasJsonLd
{
    /**
     * Get the JSON-LD record attached to this model.
     *
     * @return MorphOne<Model, $this>
     */
    public function jsonLd(): MorphOne
    {
        return $this->morphOne(OiMetadata::jsonLdModel(), 'metable');
    }

    /**
     * Create or update the JSON-LD record from Schema builders, a JsonLdData
     * object, or raw arrays.
     */
    public function syncJsonLd(JsonLdData|Schema|array $data): Model
    {
        return JsonLdFacade::update($this, $data);
    }

    /**
     * Render this model's structured data as `<script type="application/ld+json">` blocks.
     */
    public function renderJsonLd(): string
    {
        return JsonLdFacade::render($this)->toHtml();
    }
}
