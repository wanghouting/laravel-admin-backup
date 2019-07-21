<?php

namespace LTBackup\Extension\Support;

use LTBackup\Extension\Facades\FormBuilder;
use LTBackup\Extension\Facades\SettingFacade;
use LTBackup\Extension\Tools\Form\Form;
use LTBackup\Extension\Tools\Tab\Tab;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * 后台设置页面构造器
 * @author  wanghouting
 * @package LTBackup\Extension\Support\SettingViewBuilder
 */
class SettingViewBuilder {

    /**
     * 创建后台设置view
     * @param  $model
     * @param Request $request
     * @param Collection $settingTypes 后台设置类型集合
     * @param array $settings  分组的后台设置列表
     * @return Tab
     * @deprecated  please use LTBackup\Extension\Facades\SettingBuilder::buildSetting
     */
    public function buildSetting($model,  Request $request, Collection $settingTypes ,array $settings) {
        //获取当前tab索引
        $tabIndex = (integer)$request->get('t',0);
        //创建个tab选项卡
        $tab = new Tab();
        //遍历所有的分类
        foreach ($settingTypes as $type){
            $settingForType = isset($settings[$type->id]) ? $settings[$type->id] : [];
            //创建请求链接
            $uri =  explode('?',rtrim($request->getRequestUri(),'/'))[0] ;
            $thisTabIndex = $type->id - 1;
            $requestUrl =  $thisTabIndex <= 0 ? $uri : $uri .'?t=' . $thisTabIndex;
            //创建对应的form表单
            $formContent =  $this->buildSettingFormForType($model, $requestUrl , $settingForType);
            //将tab选项卡名称和内容关联
            $tab->add($type->name, $formContent);
        }
        $tab->setActive($tabIndex);
        //返回构建的内容
        return $tab;
    }

    /**
     * 根据类型构造form表单
     * @param  $model
     * @param string $actionUri
     * @param array $settings  给类型下的所有设置
     * @return string  返回构建的表单
     */
    private function buildSettingFormForType($model,$actionUri , array $settings){


        //构建表单
        $form = FormBuilder::buildFrom($model,function (Form $form) use ($settings,$actionUri){
            if(has_disabled_functions('exec'))
                $form->iNotice('exec函数已被禁用，请在php.ini配置文件下的disable_functions中去除exec','<font color="#db7093">注意</font>');
            $backupDir = SettingFacade::get('ltbackup_dir','/backups');
            if(!is_writeable($backupDir))
                $form->iNotice($backupDir.'目录不可写','<font color="#db7093">注意</font>');

            if(count($settings) > 0)
                $form->hidden('type')->value($settings[0]->type);
            foreach ($settings as $setting){
                $formType = $setting->form;
                $extra = json_decode($setting->extra);
                switch ($formType){
                    case 'switch': //开关
                        $switchValue = $setting->plainValue == 'on' ? 1 : 0;

                        $switchStatus = $extra->switch ?? get_switch_state();
                        $formInstance = $form->$formType($setting->name,$setting->cname)->states($switchStatus)->value($switchValue);
                        break;
                    case 'number': //数字
                        $numberValueMin = $extra->min ?? PHP_INT_MIN;
                        $numberValueMax = $extra->max ?? PHP_INT_MAX;
                        $formInstance =  $form->$formType($setting->name,$setting->cname)->value($setting->plainValue)->min($numberValueMin)->max($numberValueMax);
                        break;
                    case 'image':
                        $formInstance = $form->$formType($setting->name,$setting->cname)->value($setting->plainValue)->options(['deleteUrl'=>explode('?',$actionUri)[0].'/'.$setting->id ,'deleteExtraData'=>['key'=>$setting->name,'value'=>'','_token'=> csrf_token(),'_method'=> 'PUT',]]);
                        break;
                    case 'radio':
                        $formInstance = $form->$formType($setting->name,$setting->cname)->value($setting->plainValue)->options($extra->options);
                        break;
                    default:
                        $formInstance = $form->$formType($setting->name,$setting->cname)->value($setting->plainValue)->required();
                        break;
                }
                if(isset($extra->help)) $formInstance->help($extra->help);
            }
        })->tools()->footer()->setTitle(' ')->setAction($actionUri)->get();
        return $form->render();
    }



}
