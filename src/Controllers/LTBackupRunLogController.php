<?php

namespace LTBackup\Extension\Controllers;

use Encore\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\RunLog;
use LTBackup\Extension\Facades\DynamicOutput;
use LTBackup\Extension\Facades\GridBuilder;
use LTBackup\Extension\Facades\LTBackup;
use LTBackup\Extension\Facades\SettingFacade;
use LTBackup\Extension\Support\DynamicOutputSupport;
use LTBackup\Extension\Support\FtpManagerSupport;
use LTBackup\Extension\Tools\Buttons\RunLogButton;
use LTBackup\Extension\Tools\Buttons\RunLogDeleteButton;
use LTBackup\Extension\Tools\Grid\Actions;
use LTBackup\Extension\Tools\Grid\Grid;
use LTBackup\Extension\Tools\Layout\Content;


/**
 * 备份控制器
 * Class LTBackupRunLogController
 * @author wanghouting
 */
class LTBackupRunLogController extends Controller
{
    public function __construct()
    {
        $this->header = '运行日志';
    }


    public function index(Content $content){
        $content->setView('laravel-admin-backup::iframe.content');
        $grid = $this->grid();
        $this->reloadData($grid);

        $content = $content->init('','',$grid->render(),'');
        return $content;
    }


    private function reloadData(Grid $grid){
        $token = csrf_token();
        $tableId = isset($grid->tableID) ? '#'.$grid->tableID : '.table';
        $route_prefix = config('laravel-admin-backup.route_prefix');
        $script = <<<EOT
          function refresh() { 
            var needUpdateCount = 0;
            var needUpdateElement  = [];
           $("{$tableId} tbody tr").each(function(){
              var status =   $(this).find('td label.status').data('value');
              if(status <= 1 ){
                var id =  $(this).find('td label.status').data('id');
                 needUpdateElement[id] = $(this);
              } 
          });
          if(needUpdateElement.length > 0){
          
            var element = needUpdateElement.pop(); 
            var id =  element.find('td label.status').data('id');
            var status =  element.find('td label.status').data('value');
             var intv = setInterval(function(){
                $.ajax({
                    url:"/{$route_prefix}/ltbackup-refresh",
                    dataType:"json",
                    async:true,
                    data:{"id":id,"status":status,"_token":"{$token}"},
                    type:"POST",
                    success:function(req){
                        if(req.code == 200){
                 
                            if(status != req.data.status){//更新数据
                                 element.find('td label.running_at').text(req.data.running_at);
                                 var statusLabel = '';
                                switch(req.data.status){
                                    case 1:
                                        statusLabel = '<label class="label status label-info" data-id="'+id+'" data-value="'+req.data.status+'"><i class="fa fa-spinner fa-pulse "></i> 正在执行</label>';
                                        element.find('td label.status').parent().empty().append(statusLabel);
                                        break;
                                    case 2:
                                        element.find('td label.updated_at').text(req.data.updated_at);
                                        element.find('td label.file').text(req.data.file);
                                        element.find('td label.filesize').text(req.data.filesize);
                                        statusLabel = '<label class="label status label-success" data-id="'+id+'" data-value="'+req.data.status+'"> 执行完成</label>';
                                        element.find('td label.status').parent().empty().append(statusLabel);
                                        
                                        break;
                                    case 3:
                                        element.find('td label.updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label status label-danger" data-id="'+id+'" data-value="'+req.data.status+'"> 执行失败</label>';
                                         element.find('td label.status').parent().empty().append(statusLabel);
                                        break;
                                    case 4:
                                        element.find('td label.updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label status label-warning" data-id="'+id+'" data-value="'+req.data.status+'"> 用户停止</label>';
                                        element.find('td label.status').parent().empty().append(statusLabel);
                                        break;    
                                    default:
                                        element.find('td label.updated_at').text(req.data.updated_at);
                                        statusLabel = '<label class="label status label-warning" data-id="'+id+'" data-value="'+req.data.status+'"> 未知</label>';
                                        element.find('td label.status').parent().empty().append(statusLabel);
                                        break;            
                                }
                            }
                            
                            if(req.data.status >= 2){
                                var ftpLabelStatus = '';
                                switch(req.data.ftp_status){
                                    case 1:
                                        ftpLabelStatus = '<label class="label ftp_status label-primary"><i class="fa fa-exclamation-triangle"> </i> 尚未开启</label>';    
                                        break;
                                    case 2:
                                        ftpLabelStatus = '<label class="label ftp_status label-success "><a href="/{$route_prefix}/ltbackup-download?id='+id+'&type=ftp" target="_blank" style="color: #FFFFFF"> <i class="fa fa-download"> </i> 点击下载</a></label>';
                                        break;
                                    case 3:
                                        ftpLabelStatus = '<label class="label ftp_status label-danger"  ><i class="fa fa-close"> </i> 上传失败</label>';   
                                        break;
                                    case 4:
                                        ftpLabelStatus = '<label class="label ftp_status label-warning"><i class="fa fa-stop"> </i> 用户停止</label>';   
                                        break;
                                    default:
                                        break;            
                                }
                                 element.find('td label.ftp_status').parent().empty().append(ftpLabelStatus);    
                                clearInterval(intv);
                                refresh();
                            }
                        }else{
                                clearInterval(intv);
                                refresh();
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


    public function fileDownload(Request $request){
        $id = $request->get('id',0);
        $type = $request->get('type','');
        if(!$id) return false;
        $log =  RunLog::find($id);
        if(empty($type) && file_exists($log->file)){
            return response()->download($log->file);
        }else if($type == 'ftp'){
              $fileArr = explode('/',$log->ftp_file);
              $fileName = $fileArr[count($fileArr) - 1];
              $distFile = LTBackup::getTmpDir() . '/ftp_' .$fileName;
             file_put_contents($distFile,FtpManagerSupport::getInstance()->downloadFile($log->ftp_file)) ;
             return response()->download($distFile);
        }
        return  false;

    }
    /**
     * 运行日志记录
     * @return Grid
     */
    protected  function grid(){
        return   GridBuilder::buildGrid(RunLog::class,function (Grid $grid){
            $grid->model()->orderBy('id','desc');
            $grid->disableCreateButton();
            $grid->id('ID');
            $grid->column('rule.name','规则名称');
            $grid->column('run_type','触发方式')->display(function ($value){
                return $value == RunLog::RUN_TYPE_AUTO ? '<label class="label label-primry">定时备份</label>': '<label class="label label-warning">手动执行</label>';
            });
            $grid->column('created_at','创建时间');
            $grid->column('running_at','执行时间')->display(function ($value){
                return LTBackup::getColumnLabel('running_at',$value);
            });
            $grid->column('updated_at','结束时间')->display(function ($value) {
                $value =   $this->status <= 1 ? '' : $value;
                return LTBackup::getColumnLabel('updated_at',$value);
            });
            $grid->column('file','本地文件(点击下载;有删除线的文件已被删除或过期被清理)')->display(function ($value){
                $value = $this->status == RunLog::RUN_STATE_SUCCESS  ? $value : '';
                return  LTBackup::getColumnLabel('file',$value,$this->id);
            });
            
            $grid->column('ftp_status','ftp上传')->display(function ($value){
                return LTBackup::getFtpStateLabel($value,$this->id);
            });
            $grid->column('filesize','文件大小')->display(function($value){
                $value = $this->status == RunLog::RUN_STATE_SUCCESS  ? ( $value == 0 ? '' : $value) : '';
                return  LTBackup::getColumnLabel('filesize',$value);
            });

            $grid->column('status','状态')->display(function ($value){
                return LTBackup::getRunStateLabel($value,$this->id);
            });
            $grid->actions(function (Actions $actions){
                $actions->disableEdit();
                //$actions->disableDelete();
                $actions->setResource('/'.config('laravel-admin-backup.route_prefix').'/ltbackup-log');
                $actions->disableView();
               // $actions->prepend((new RunLogDeleteButton( $actions->getKey()))->render());
                $actions->prepend((new RunLogButton( $actions->getKey()))->render());
            });

        })->get();

    }


    public function store()
    {
        try{
            $rule_id = request()->get('rule_id',0);
            if(!$rule_id) throw new \Exception('参数错误');
            LTBackup::addRun($rule_id);
            return response()->json(['code'=>200,'message'=>'加入队列成功']);
        }catch (\Exception $e){
            return response()->json(['code'=>422,'message'=>$e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try{
            RunLog::where('id',$id)->delete();
            return response()->json(['code'=>200,'status'=>1,'message'=>'删除成功']);
        }catch (\Exception $e){
            return response()->json(['status'=>0,'code'=>422,'message'=>$e->getMessage()]);
        }

    }

    public function logView(Request $request)
    {
        $id = $request->get('id',0);
        $file = LTBackup::getLogFile($id);
        if(!file_exists($file)){
            DynamicOutput::addClause('error:  日志文件不存在！' ,DynamicOutputSupport::TYPE_MESSAGE);
            DynamicOutput::output();
        }else{
            $fp = fopen($file, "r") ;
            $lineStr = '';
            $readCount = 0;
            while(!feof($fp))
            {
                $lineStr =  fgets($fp);
                $readCount +=  strlen($lineStr);
                DynamicOutput::addClause( $lineStr,DynamicOutputSupport::TYPE_MESSAGE);

            }
            DynamicOutput::output(true,false,false);
            fclose($fp);
            while(trim($lineStr) != \LTBackup\Extension\Support\LTBackup::LOG_END ) {
                $fp = fopen($file, "r") ;
                fseek($fp,$readCount);
                $start  = 0;
                while(!feof($fp))
                {
                    $start++;
                    $lineStr =  fgets($fp);
                    $readCount +=  strlen($lineStr);
                    DynamicOutput::addClause($lineStr, DynamicOutputSupport::TYPE_MESSAGE);
                    DynamicOutput::output(false,false,true);

                }
                fclose($fp);
                $start == 0 && sleep(1);
            }
            DynamicOutput::addClause(\LTBackup\Extension\Support\LTBackup::LOG_END, DynamicOutputSupport::TYPE_MESSAGE);
            DynamicOutput::output(false,true,true);

        }

    }

    public function refresh(Request $request)
    {
        $id = $request->get('id',0);
        $status = $request->get('status',-1);

        if(!$id || $status == -1){
            return response()->json(['code'=>500,'message'=>'参数错误']);
        }

        $res =  RunLog::query()->find($id);
        if(!$res){
            return response()->json(['code'=>500,'message'=>'数据不存在']);
        }
        if($res->status == RunLog::RUN_STATE_SUCCESS){
            $res->filesize = trans_byte($res->filesize);
        }
        return response()->json(['code'=>200,'message'=>'获取成功','data'=>$res]);
    }


}