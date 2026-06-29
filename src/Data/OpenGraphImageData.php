<?php

namespace OiLab\OiLaravelMetadata\Data;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;

/**
 * Data transfer object describing the image of an Open Graph object.
 */
class OpenGraphImageData extends Data
{
    public function __construct(
        #[Nullable]
        public ?string $url = null,
        #[Nullable]
        public ?int $width = null,
        #[Nullable]
        public ?int $height = null,
    ) {}
}
