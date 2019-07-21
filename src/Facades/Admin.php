<?php

namespace LTBackup\Extension\Facades;


use Illuminate\Support\Facades\Facade;
use LTBackup\Extension\Tools\Grid\Grid;
use LTBackup\Extension\Tools\Layout\Form;

/**
 * Class Admin.
 * @author wanghouting
 * @method static Grid grid($model, \Closure $callable)
 * @method static Form form($model, \Closure $callable)
// * @method static \Modules\Admin\Show\Show show($model, \Closure $callable);
 * @method static menu();
 */

class Admin extends Facade {

    protected static function getFacadeAccessor() {
        return \LTBackup\Extension\Tools\Admin::class;
    }
}
