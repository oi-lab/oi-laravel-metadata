<?php

namespace OiLab\OiLaravelMetadata\Facades;

use Illuminate\Support\Facades\Facade;
use OiLab\OiLaravelMetadata\Services\OgService;

/**
 * @method static \OiLab\OiLaravelMetadata\Models\OpenGraph|null forModel(\Illuminate\Database\Eloquent\Model $model)
 * @method static \OiLab\OiLaravelMetadata\Data\OpenGraphData toData(\Illuminate\Database\Eloquent\Model $model)
 * @method static \OiLab\OiLaravelMetadata\Models\OpenGraph update(\Illuminate\Database\Eloquent\Model $model, \OiLab\OiLaravelMetadata\Data\OpenGraphData $data)
 * @method static \Illuminate\Support\HtmlString render(\Illuminate\Database\Eloquent\Model|\OiLab\OiLaravelMetadata\Data\OpenGraphData|null $source = null)
 *
 * @see OgService
 */
class Og extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return OgService::class;
    }
}
