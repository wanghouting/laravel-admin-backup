<?php

namespace LTBackup\Extension\Controllers;

use Encore\Admin\Admin;
use Illuminate\Http\Request;
use LTBackup\Extension\Controllers\Base\AdminBaseController;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\RunLog;
use LTBackup\Extension\Facades\FormBuilder;
use LTBackup\Extension\Facades\GridBuilder;
use LTBackup\Extension\Facades\LTBackup;
use LTBackup\Extension\Facades\SettingFacade;
use LTBackup\Extension\Tools\Buttons\RunButton;
use LTBackup\Extension\Tools\Buttons\RunLogButton;
use LTBackup\Extension\Tools\Form\Form;
use LTBackup\Extension\Tools\Grid\Actions;
use LTBackup\Extension\Tools\Grid\Grid;
use LTBackup\Extension\Tools\Layout\Content;


/**
 * 备份控制器
 * Class LTBackupRuleController
 * @author wanghouting
 */
class LTBackupRuleController extends AdminBaseController
{

    public function __construct()
    {
        $this->header = '备份规则';
    }


    public function index(Content $content)
    {
        $gridLog = $this->gridLog();
        $this->reloadData($gridLog);
        $content = $content->init($this->header,trans('admin.list'),$this->grid()->render().$gridLog->render());
        $error = '';
        if(has_disabled_functions('exec'))
            $error = 'exec函数已被禁用，请在php.ini配置文件下的disable_functions中去除exec';

        $backupDir = SettingFacade::get('ltbackup_dir','/backups');
        if(!is_dir($backupDir)){
            @mkdir($backupDir);
        }
        if(!is_writeable($backupDir))
            $error .= "</br></br> ".$backupDir.'目录不可写';
        if(!empty($error))
            $content = $content->withError('警告',$error);
        return $content;
    }


    private function reloadData(Grid $grid){
        $token = csrf_token();
        $script = <<<EOT
         
          function refresh() { 
            var needUpdateCount = 0;
            var needUpdateElement  = [];
           $("#{$grid->tableID } tbody tr").each(function(){
              var status =  $(this).find('.column-status').find('label').data('value');
              if(status <= 1 ){
                   
                var id =  $(this).find('.column-status').find('label').data('id');
                 needUpdateElement[id] = $(this);
              } 
          });
          if(needUpdateElement.length > 0){
          
            var element = needUpdateElement.pop(); 
            var id =  element.find('.column-status').find('label').data('id');
            var status =  element.find('.column-status').find('label').data('value');
           
             var intv = setInterval(function(){
                $.ajax({
                    url:"/admin/ltbackup-refresh",
                    dataType:"json",
                    async:true,
                    data:{"id":id,"status":status,"_token":"{$token}"},
                    type:"POST",
                    success:function(req){
                        if(req.code == 200){
                        console.log(req.data.status);
                            if(status != req.data.status){//更新数据
                                 element.find('.column-running_at').text(req.data.running_at);
                                 var statusLabel = '';
                                switch(req.data.status){
                                    case 1:
                                        statusLabel = '<label class="label label-info" data-id="'+id+'" data-value="'+req.data.status+'"><i class="fa fa-spinner fa-pulse "></i> 正在执行</label>';
                                        element.find('.column-status').empty().append(statusLabel);
                                        break;
                                    case 2:
                                        element.find('.column-updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label label-success" data-id="'+id+'" data-value="'+req.data.status+'"> 执行完成</label>';
                                        element.find('.column-status').empty().append(statusLabel);   
                                        break;
                                    case 3:
                                        element.find('.column-updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label label-danger" data-id="'+id+'" data-value="'+req.data.status+'"> 执行失败</label>';
                                        element.find('.column-status').empty().append(statusLabel);  
                                        break;
                                    case 4:
                                        element.find('.column-updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label label-warning" data-id="'+id+'" data-value="'+req.data.status+'"> 用户停止</label>';
                                        element.find('.column-status').empty().append(statusLabel);
                                        break;    
                                    default:
                                        element.find('.column-updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label label-warning" data-id="'+id+'" data-value="'+req.data.status+'"> 未知</label>';
                                        element.find('.column-status').empty().append(statusLabel);
                                        break;            
                                }
                            }
                            
                            if(req.data.status >= 2){
                                clearInterval(intv);
                                refresh();
                            }
                        }
                     },
                    error:function(){
                    }

                });            
             },5000)
           }
        }
            
        refresh();
EOT;
        Admin::script($script);
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return  GridBuilder::buildGrid(Rule::class,function (Grid $grid){
            $grid->id('ID')->sortable();
            $grid->name('规则名称');
            $grid->column('time_at','执行时间')->display(function ($value){
                return '<label class="label label-warning">'.$value.'</label>';
            });
            $grid->column('period','执行周期')->display(function ($value){
                switch ($value){
                    case -1:
                        return '每天';
                    case 0:
                        return '一次';
                    default:
                        return $this->period_days.'天';
                }
            });
            $grid->iSwitch('status','状态');
            $grid->column('next_run','下次执行时间')->display(function ($value){
                if(!$this->status) return ''; //如果status == 0 表示已关闭，则没有下次执行时间
                if($value) return $value;    //如果不为null,则直接显示
                return LTBackup::getNextRunTime($this->time_at);

            });
            $grid->column('run_times','执行次数');
            $grid->column('success_times','成功次数')->display(function (){
                return  $this->run_times - $this->run_fail_times;
            });
            $grid->column('run_fail_times','失败次数');

            $grid->created_at('创建时间');
            $grid->actions(function (Actions $actions){
                $actions->disableView();
                $actions->prepend((new RunButton( $actions->getKey()))->render());
            });
        })->get();
        
    }


    /**
     * 运行日志记录
     * @return Grid
     */
    protected  function gridLog(){
        return   GridBuilder::buildGrid(RunLog::class,function (Grid $grid){
            $grid->model()->orderBy('id','desc');
            $grid->disableCreateButton();
            $grid->id('ID');
            $grid->column('rule.name','规则名称');
            $grid->column('created_at','创建时间');
            $grid->column('running_at','执行时间');
            $grid->column('updated_at','结束时间')->display(function ($value) {
                return  $this->status <= 1 ? '' : $value;
            });
            $grid->column('file','文件');
            $grid->column('status','状态')->display(function ($value){
                return LTBackup::getRunStateLabel($value,$this->id);
            });
            $grid->actions(function (Actions $actions){
                $actions->disableEdit();
                $actions->setResource('/admin/ltbackup-log');
                $actions->disableView();
                $actions->prepend((new RunLogButton( $actions->getKey()))->render());
            });

        })->get();

    }

    protected function form()
    {
        $data = Rule::find($this->id);
        return FormBuilder::buildFrom(Rule::class,function (Form $form) use ($data){
            $period_days = $data ? ($data->period_days ? $data->period_days : 7) : 7;
            $backup_path = $data && !empty($data->path) ? $data->path : base_path();

            $form->iNotice('网站所在根目录为：<b>'.base_path().'</b>','<font color="#db7093">提示</font>');
            $form->text('name','规则名称')->required()->setWidth(3);
            $form->iRadio('type','类型')->options(Rule::RULE_TYPE)->lastInput($form,'path','text',['style'=>'width:500px;','value'=>$backup_path],'请填写需要备份的路径')->default(0);
            $form->time('time_at','执行时间')->required()->default('00:00:00');
            $form->iRadio('period','执行周期')->options(Rule::RULE_PERIOD)->lastInput($form,'period_days','number',['min'=>2,'value'=>$period_days])->default(-1);
            $form->iSwitch('status','状态');
            $form->saving(function (Form $form){
                if($form->model()->id ){ //编辑，判断时间\状态是否变化
                    $status = $form->status == 'on' ? 1 : 0;
                    if($status != $form->model()->status
                       || $form->time_at != $form->model()->time_at
                       || $form->period != $form->model()->period
                       || ($form->period == -99 &&  $form->period_days != $form->model()->period_days )){
                        //变化了，需要重置next_run
                       $form->model()->next_run = LTBackup::getNextRunTime($form->time_at);
                   }
                }else{
                    $form->model()->next_run = LTBackup::getNextRunTime($form->time_at);
                }
            });
            $form->saved(function (){
            });
        })->tools(true,true,false)->footer()->get();
    }


}