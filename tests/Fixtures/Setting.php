<?php

namespace OiLab\OiLaravelMetadata\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    public $timestamps = false;
}
