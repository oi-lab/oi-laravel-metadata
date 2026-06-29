<?php

namespace OiLab\OiLaravelMetadata\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing the Open Graph representation of a resource.
 */
class OpenGraphData extends Data
{
    public function __construct(
        #[Nullable, Max(255)]
        public ?string $type = null,
        #[Nullable, Max(255)]
        public ?string $title = null,
        #[Nullable]
        public ?string $description = null,
        #[Nullable, Max(2048)]
        public ?string $url = null,
        public ?OpenGraphImageData $image = null,
    ) {}
}
