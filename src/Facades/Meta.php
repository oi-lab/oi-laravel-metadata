<?php

namespace OiLab\OiLaravelMetadata\Facades;

use Illuminate\Support\Facades\Facade;
use OiLab\OiLaravelMetadata\Services\MetaService;

/**
 * @method static \OiLab\OiLaravelMetadata\Models\Metadata|null forModel(\Illuminate\Database\Eloquent\Model $model)
 * @method static \OiLab\OiLaravelMetadata\Data\MetadataData toData(\Illuminate\Database\Eloquent\Model $model)
 * @method static \OiLab\OiLaravelMetadata\Models\Metadata update(\Illuminate\Database\Eloquent\Model $model, \OiLab\OiLaravelMetadata\Data\MetadataData $data)
 * @method static \Illuminate\Support\HtmlString render(\Illuminate\Database\Eloquent\Model|\OiLab\OiLaravelMetadata\Data\MetadataData|null $source = null)
 *
 * @see MetaService
 */
class Meta extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MetaService::class;
    }
}
