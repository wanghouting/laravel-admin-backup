<?php

namespace LTBackup\Extension\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use LTBackup\Extension\Entities\Setting;
use LTBackup\Extension\Entities\SettingTypes;
use LTBackup\Extension\Tools\Layout\Content;
use LTBackup\Extension\Facades\SettingBuilder;






/**
 * 备份设置控制器
 * Class LTBackupSettingController
 * @author wanghouting
 */
class LTBackupSettingController extends Controller
{



    protected $header = "备份设置";


    /**
     * @param Request $request
     * @param Content $content
     * @return \Encore\Admin\Layout\Content
     */
    public function index(Request $request, Content $content) {
        if($request->isMethod("post")) {
            $input = $request->all();  //获取所有的提交数据

            $requestUrl = rtrim($request->getRequestUri(),'/');
            //数据验证
            $validator = Validator::make($input, $this->rules(),$this->messages());

            if ($validator->fails()) {
                return redirect($requestUrl)
                    ->withErrors($validator)
                    ->withInput();
            }else{
                //子类数据处理
                $this->extra($input);
                //更新数据
                foreach ($input as $settingName => $settingValue) {

                    if($setting = Setting::where('name',$settingName)->first()){
                        $setting->plainValue = $settingValue;
                        $setting->save();
                    }
                }
                admin_success("提示", "更新成功");
                return redirect($requestUrl);
            }
        }else{
            //获取所有的后台设置类型
            $settingTypes = SettingTypes::all();
            //获取按照后台设置类型分组的设置列表
            $allSettings = Setting::all();
            $settings = [];
            foreach ($allSettings as $setting){
                $settings[$setting->type][] = $setting;
            }

            //构建渲染页面并返回
            return  $content->init($this->header,'设置',SettingBuilder::buildSetting(Setting::class,$request,$settingTypes,$settings));
        }
    }


    /**
     * @param array $input
     */
    protected function extra(array  &$input){

    }


    /**
     * 规则错误提示
     * @return array
     */
    protected function messages()
    {
        return [];
    }

    /**
     * 规则
     * @return array
     */
    protected function rules()
    {
        return [];
    }


}