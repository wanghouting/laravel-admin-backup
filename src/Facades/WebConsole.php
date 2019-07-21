<?php


namespace LTBackup\Extension\Facades;


use Illuminate\Support\Facades\Facade;
use LTBackup\Extension\Support\WebConsoleSupport;

/**
 * Class WebConsole
 * @method  static  execute_command($command)
 * @package LTBackup\Extension\Facades
 */
class WebConsole extends Facade
{
    public static function getFacadeAccessor()
    {
        return WebConsoleSupport::class;
    }
}