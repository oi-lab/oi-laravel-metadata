<?php

namespace OiLab\OiLaravelMetadata\Services;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use OiLab\OiLaravelMetadata\Data\JsonLdData;
use OiLab\OiLaravelMetadata\Models\JsonLd;
use OiLab\OiLaravelMetadata\Support\Schema;
use OiLab\OiLaravelMetadata\Support\SeoContext;

/**
 * JsonLdService
 *
 * Reads, writes, and renders the JSON-LD structured data of a model. The model
 * must use the HasJsonLd trait (or otherwise expose a `jsonLd` MorphOne
 * relation). Structured data is rendered as one or more
 * `<script type="application/ld+json">` blocks, safe to embed in the document
 * `<head>` or `<body>`.
 */
class JsonLdService
{
    public function __construct(protected SeoContext $context) {}

    /**
     * Get the JSON-LD record attached to a model, if any.
     */
    public function forModel(Model $model): ?JsonLd
    {
        /** @var JsonLd|null $jsonLd */
        $jsonLd = $model->jsonLd()->first();

        return $jsonLd;
    }

    /**
     * Build a JsonLdData object from a model's stored structured data.
     */
    public function toData(Model $model): JsonLdData
    {
        $record = $this->forModel($model);

        return new JsonLdData($record?->graphs ?? []);
    }

    /**
     * Create or update the JSON-LD record of a model from Schema builders,
     * a JsonLdData object, or raw arrays.
     */
    public function update(Model $model, JsonLdData|Schema|array $data): JsonLd
    {
        /** @var JsonLd $jsonLd */
        $jsonLd = $model->jsonLd()->updateOrCreate([], [
            'graphs' => $this->resolveGraphs($data),
        ]);

        return $jsonLd;
    }

    /**
     * Render structured data as `<script type="application/ld+json">` blocks.
     */
    public function render(Model|JsonLdData|Schema|array|null $source = null): HtmlString
    {
        $source ??= $this->context->subject('jsonLd');

        $scripts = [];

        foreach ($this->resolveGraphs($source) as $graph) {
            if ($graph === []) {
                continue;
            }

            $json = $this->encode($this->withContext($graph));

            if ($json === null) {
                continue;
            }

            $scripts[] = '<script type="application/ld+json">'.$json.'</script>';
        }

        return new HtmlString(implode("\n", $scripts));
    }

    /**
     * Normalize any accepted source into a list of plain graph arrays.
     *
     * @return list<array<string, mixed>>
     */
    protected function resolveGraphs(Model|JsonLdData|Schema|array|null $source): array
    {
        $graphs = match (true) {
            $source === null => [],
            $source instanceof JsonLdData => $source->graphs,
            $source instanceof Schema => [$source],
            $source instanceof Model => $this->toData($source)->graphs,
            default => $this->splitGraphs($source),
        };

        return array_values(array_map(
            fn (mixed $graph): array => $this->normalize($graph),
            $graphs,
        ));
    }

    /**
     * Decide whether an array is a list of graphs or a single graph.
     *
     * @param  array<mixed>  $source
     * @return list<mixed>
     */
    protected function splitGraphs(array $source): array
    {
        if ($source === []) {
            return [];
        }

        $isListOfGraphs = array_is_list($source) && array_reduce(
            $source,
            static fn (bool $carry, mixed $item): bool => $carry && (is_array($item) || $item instanceof Schema),
            true,
        );

        return $isListOfGraphs ? array_values($source) : [$source];
    }

    /**
     * Resolve a graph into a plain array, recursively unwrapping nested
     * Schema nodes and arrayables.
     *
     * @return array<string, mixed>
     */
    protected function normalize(mixed $graph): array
    {
        $resolved = $this->resolveValue($graph);

        return is_array($resolved) ? $resolved : [];
    }

    /**
     * Recursively resolve Schema nodes and arrayables to scalars/arrays.
     */
    protected function resolveValue(mixed $value): mixed
    {
        if ($value instanceof Schema || $value instanceof Arrayable) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->resolveValue($item), $value);
        }

        return $value;
    }

    /**
     * Ensure the top-level `@context` is present.
     *
     * @param  array<string, mixed>  $graph
     * @return array<string, mixed>
     */
    protected function withContext(array $graph): array
    {
        if (array_key_exists('@context', $graph)) {
            return $graph;
        }

        return ['@context' => config('oi-laravel-metadata.json_ld.context', 'https://schema.org')] + $graph;
    }

    /**
     * JSON-encode a graph with flags that keep it safe inside a `<script>` tag.
     *
     * @param  array<string, mixed>  $graph
     */
    protected function encode(array $graph): ?string
    {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;

        if (config('oi-laravel-metadata.json_ld.pretty', false)) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $json = json_encode($graph, $flags);

        return $json === false ? null : $json;
    }
}
