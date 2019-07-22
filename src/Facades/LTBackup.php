<?php
namespace LTBackup\Extension\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class LTBackup
 * @method  static  string getRunStateLabel($state);
 * @method static void run(bool $all);
 * @method  static string getNextRunTime($time_at);
 * @method  static  void  runLog($id,$log)
 * @package LTBackup\Extension\Facades
 */
class LTBackup extends  Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \LTBackup\Extension\Support\LTBackup::class;
    }
}