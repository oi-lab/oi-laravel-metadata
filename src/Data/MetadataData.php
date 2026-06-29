<?php

namespace OiLab\OiLaravelMetadata\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing the SEO metadata of a resource.
 */
class MetadataData extends Data
{
    /**
     * @param  list<string>  $keywords
     */
    public function __construct(
        #[Nullable, Max(255)]
        public ?string $title = null,
        #[Nullable]
        public ?string $description = null,
        public array $keywords = [],
        #[Nullable, Max(255)]
        public ?string $author = null,
        #[Nullable, Max(255)]
        public ?string $copyright = null,
        #[Nullable, Max(10)]
        public ?string $language = null,
        #[Nullable, Max(255)]
        public ?string $revisit_after = null,
        #[Nullable, Max(255)]
        public ?string $robots = null,
        #[Nullable, Max(255)]
        public ?string $googlebot = null,
    ) {}
}
