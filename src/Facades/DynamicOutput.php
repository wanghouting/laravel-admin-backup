<?php
/**
 * Created by PhpStorm.
 * User: wanghouting
 * Date: 2019-07-21
 * Time: 15:02
 */

namespace LTBackup\Extension\Facades;


use Illuminate\Support\Facades\Facade;
use LTBackup\Extension\Support\DynamicOutputSupport;

/**
 * Class DynamicOutput
 * @author wanghouting
 * @method  static addClause($content,$type)
 * @method  static getClause()
 * @method  static output(... $clauses)
 * @package LTBackup\Extension\Facades
 */
class DynamicOutput extends Facade {
    protected static function getFacadeAccessor() {
        return DynamicOutputSupport::class;
    }
}