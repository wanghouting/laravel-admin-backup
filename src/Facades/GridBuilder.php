<?php

namespace LTBackup\Extension\Facades;


use Closure;
use Illuminate\Support\Facades\Facade;
use LTBackup\Extension\Support\GridViewBuilder;

/**
 * Class GridBuilder
 * @method static GridViewBuilder buildGrid($model,Closure $callback )
 * @package Modules\Admin\Facades
 */
class GridBuilder extends Facade {
    protected static function  getFacadeAccessor() {
        return GridViewBuilder::class;
    }
}
