<?php

namespace LTBackup\Extension\Controllers\Base;


use Encore\Admin\Controllers\HasResourceActions;
use Illuminate\Routing\Controller;
use LTBackup\Extension\Facades\SettingFacade;
use Modules\Admin\Tools\Form\Form;
use Modules\Admin\Tools\Grid\Grid;
use Modules\Admin\Tools\Layout\Content;
use Modules\Admin\Tools\Show\Show;

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
        if(has_disabled_functions('exec'))
            $error = 'exec函数已被禁用，请在php.ini配置文件下的disable_functions中去除exec';

        $backupDir = SettingFacade::get('ltbackup_dir','/backups');

        if(!is_writeable($backupDir))
            $error .= "</br></br> ".$backupDir.'目录不可写';
        if(!empty($error))
            $content = $content->withError('警告',$error);
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
     * @return Show
     */
    protected function detail($id) {
        $this->id = $id;
        throw new RuntimeException('Controller does not implement detail method.');
    }
    
    /**
     * @return Grid
     */
    protected function grid() {
        throw new RuntimeException('Controller does not implement grid method.');
    }

    /**
     * @return Form
     */
    protected function form() {
        throw new RuntimeException('Controller does not implement form method.');
    }


}
