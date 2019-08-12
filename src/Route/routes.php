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
    'prefix' => config('laravel-admin-backup.route_prefix'),
    'middleware' => ['web', 'admin']
], function (Router $router) {
    $router->resource('ltbackup-rule','LTBackupRuleController');
    $router->post('ltbackup-refresh','LTBackupRunLogController@refresh');
    $router->get('ltbackup-log-view','LTBackupRunLogController@logView');
    $router->get('ltbackup-download','LTBackupRunLogController@fileDownload');
    $router->resource('ltbackup-log','LTBackupRunLogController');
    $router->match(['get','post'],'ltbackup-setting', 'LTBackupSettingController@index');
    $router->post('ltbackup-ftp-test-connection','LTBackupSettingController@ftpTestConnection');

});
