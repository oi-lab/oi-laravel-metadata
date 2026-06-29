<?php

namespace OiLab\OiLaravelMetadata\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use OiLab\OiLaravelMetadata\Concerns\HasMeta;

class Page extends Model
{
    use HasMeta;

    protected $table = 'pages';

    protected $guarded = [];
}
