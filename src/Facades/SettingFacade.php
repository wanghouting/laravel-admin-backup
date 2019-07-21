<?php

namespace LTBackup\Extension\Facades;

use LTBackup\Extension\Support\SettingSupport;
use Modules\Base\Facades\DbFacades;

/**
 * Class SettingFacade
 * @method  static mixed get($name, $default = null);
 * @package LTBackup\Extension\Facades
 */
class SettingFacade extends DbFacades
{
    protected static function getFacadeAccessor()
    {
        return SettingSupport::class;
    }
}
