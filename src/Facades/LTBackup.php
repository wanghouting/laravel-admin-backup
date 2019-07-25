<?php
namespace LTBackup\Extension\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class LTBackup
 * @method  static  string getRunStateLabel($state);
 * @method  static string getFtpStateLabel($state);
 * @method  static string getColumnLabel($column,$value,$id = 0);
 * @method static void run(bool $all);
 * @method  static string getNextRunTime($time_at);
 * @method static   void  addRun($rule_id);
 * @method  static  void  runLog($id,$log)
 * @method  static void clear();
 * @method  static string  getBackupDir($addExtra = true);
 * @method  static string getTmpDir();
 * @method static string getLogFile($id);
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