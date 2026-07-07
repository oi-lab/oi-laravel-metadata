<?php

namespace OiLab\OiLaravelMetadata\Data;

use OiLab\OiLaravelMetadata\Support\Schema;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing the JSON-LD structured data of a resource.
 *
 * A resource may carry several independent structured-data graphs (for example
 * an `Article`, a `BreadcrumbList`, and an `Organization`), each rendered as its
 * own `<script type="application/ld+json">` block.
 */
class JsonLdData extends Data
{
    /**
     * @param  list<array<string, mixed>>  $graphs
     */
    public function __construct(
        public array $graphs = [],
    ) {}

    /**
     * Build a JsonLdData object from Schema builders and/or raw arrays.
     */
    public static function make(Schema|array ...$graphs): self
    {
        return new self(array_values(array_map(
            static fn (Schema|array $graph): array => $graph instanceof Schema ? $graph->toArray() : $graph,
            $graphs,
        )));
    }
}
