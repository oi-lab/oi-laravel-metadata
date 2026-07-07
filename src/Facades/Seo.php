<?php

namespace OiLab\OiLaravelMetadata\Facades;

use Illuminate\Support\Facades\Facade;
use OiLab\OiLaravelMetadata\Support\SeoContext;

/**
 * @method static \OiLab\OiLaravelMetadata\Support\SeoContext for(\Illuminate\Database\Eloquent\Model|null $subject)
 * @method static void forget()
 * @method static bool has()
 * @method static \Illuminate\Database\Eloquent\Model|null subject(?string $relation = null)
 *
 * @see SeoContext
 */
class Seo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SeoContext::class;
    }
}
