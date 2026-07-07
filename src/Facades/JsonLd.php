<?php

namespace OiLab\OiLaravelMetadata\Facades;

use Illuminate\Support\Facades\Facade;
use OiLab\OiLaravelMetadata\Services\JsonLdService;

/**
 * @method static \OiLab\OiLaravelMetadata\Models\JsonLd|null forModel(\Illuminate\Database\Eloquent\Model $model)
 * @method static \OiLab\OiLaravelMetadata\Data\JsonLdData toData(\Illuminate\Database\Eloquent\Model $model)
 * @method static \OiLab\OiLaravelMetadata\Models\JsonLd update(\Illuminate\Database\Eloquent\Model $model, \OiLab\OiLaravelMetadata\Data\JsonLdData|\OiLab\OiLaravelMetadata\Support\Schema|array $data)
 * @method static \Illuminate\Support\HtmlString render(\Illuminate\Database\Eloquent\Model|\OiLab\OiLaravelMetadata\Data\JsonLdData|\OiLab\OiLaravelMetadata\Support\Schema|array|null $source = null)
 *
 * @see JsonLdService
 */
class JsonLd extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JsonLdService::class;
    }
}
