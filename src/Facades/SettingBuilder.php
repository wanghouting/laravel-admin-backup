<?php
namespace LTBackup\Extension\Facades;


use Illuminate\Support\Facades\Facade;
use LTBackup\Extension\Support\SettingViewBuilder;

/**
 * Class SettingBuilder
 * @method static \Encore\Admin\Widgets\Tab buildSetting($model,  \Illuminate\Http\Request $request, \Illuminate\Support\Collection $settingTypes ,array $settings)
 * @author wanghouting
 * @package LTBackup\Extension\Facades
 */
class SettingBuilder extends Facade {
    protected static function getFacadeAccessor() {
        return SettingViewBuilder::class;
    }
}
