<?php

namespace LTBackup\Extension\Controllers\Base;


use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Show;
use Illuminate\Routing\Controller;
use LTBackup\Extension\Facades\LTBackup;
use LTBackup\Extension\Facades\SettingFacade;


use LTBackup\Extension\Tools\Grid\Grid;
use LTBackup\Extension\Tools\Layout\Content;
use RuntimeException;




/**
 * Class AdminBaseController
 * @author wanghouting
 * @package LTBackup\Extension\Controllers\Base;
 */
class AdminBaseController extends Controller
{

    use HasResourceActions;

    protected $header;
    protected $id = 0;

    /**
     * @param Content $content
     * @return mixed
     */
    public function index(Content $content) {
        return $content->init($this->header,trans('admin.list'),$this->grid()->render());
    }

    /**
     * @param $id
     * @param Content $content
     * @return mixed
     */
    public function show($id, Content $content){
        $this->id = $id;
        return $content->init($this->header,trans('admin.detail'),$this->detail($id));
    }

    /**
     * @param $id
     * @param Content $content
     * @return mixed
     */
    public function edit($id, Content $content){
        $this->id = $id;
        $content =  $content->init($this->header,trans('admin.edit'),$this->form()->edit($id));
        $error = '';
//        if(has_disabled_functions('exec'))
//            $error = 'exec函数已被禁用，请在php.ini配置文件下的disable_functions中去除exec';

        $backupDir = LTBackup::getBackupDir(false);
        if(!is_writeable($backupDir))
            $error .= $backupDir.'目录不可写,备份功能将自动关闭';
        if(is_win()){
            $error .=  empty($error) ? '' : "; " ;
            $error .= '暂时只支持linux/mac系统,备份功能将自动关闭';
        }
        if(!empty($error)){
            $content = $content->withError('警告',$error);
            if(SettingFacade::get('ltbackup_status') == 'on'){
                SettingFacade::set('ltbackup_status','off');
            }
        }else{
//            if(SettingFacade::get('ltbackup_status') != 'on'){
//                SettingFacade::set('ltbackup_status','on');
//            }
        }
        return $content;
    }


    /**
     * @param Content $content
     * @return mixed
     */
    public function create(Content $content){
        return $content->init($this->header,trans('admin.create'),$this->form());
    }

    /**
     * @param $id
     * 
     */
    protected function detail($id) {
        $this->id = $id;
        throw new RuntimeException('Controller does not implement detail method.');
    }
    
    /**
     *
     */
    protected function grid() {
        throw new RuntimeException('Controller does not implement grid method.');
    }

    /**
     *
     */
    protected function form() {
        throw new RuntimeException('Controller does not implement form method.');
    }


}
