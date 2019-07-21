<?php
/**
 * Created by PhpStorm.
 * User: wanghouting
 * Date: 2019-06-14
 * Time: 13:40
 */
use Illuminate\Routing\Router;

Route::group([
    'namespace' => "LTBackup\\Extension\\Controllers",
    'prefix' => 'admin',
    'middleware' => ['web', 'admin']
], function (Router $router) {
    $router->resource('ltbackup-rule','\LTBackup\Extension\Controllers\LTBackupRuleController');
    $router->resource('ltbackup-log','\LTBackup\Extension\Controllers\LTBackupRunLogController');
    $router->match(['get','post'],'ltbackup-setting', '\LTBackup\Extension\Controllers\LTBackupSettingController@index');
});
