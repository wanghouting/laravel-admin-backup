<?php
namespace LTBackup\Extension\Support;
use Illuminate\Support\Facades\DB;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\RunLog;
use LTBackup\Extension\Facades\SettingFacade;

/**
 * @author wanghouting
 * Class LTBackup
 */
class LTBackup{
    

    public function getRunStateLabel($state)
    {
        switch ($state){
            case RunLog::RUN_STATE_WAITING:
                return '<label class="label label-primary"><i class="fa fa-spinner fa-pulse "></i> 正在等待</label>';
            case RunLog::RUN_STATE_RUNNING:
                return '<label class="label label-info"><i class="fa fa-spinner fa-pulse "></i> 正在执行</label>';
            case RunLog::RUN_STATE_SUCCESS:
                return '<label class="label label-success"><i class="fa fa-spinner fa-pulse "></i> 执行完成</label>';
            case RunLog::RUN_STATE_FAIL:
                return '<label class="label label-danger"><i class="fa fa-spinner fa-pulse "></i> 执行失败</label>';
            case RunLog::RUN_STATE_STOPED:
                return '<label class="label label-warning"><i class="fa fa-spinner fa-pulse "></i> 用户停止</label>';
            default:
                return '<label class="label label-warning"><i class="fa fa-spinner fa-pulse "></i> 未知</label>';
        }
    }

    /**
     * @param bool $all
     */
    public function run(bool $all)
    {
        if(SettingFacade::get('ltbackup_status') != 'on') return;

        try{
            if($all){
               $needRun =  Rule::query()->where('status',1)->where('next_run','<=',date('Y-m-d H:i:s'))->get();
               foreach ($needRun as $rule){
                   DB::transaction(function () use ($rule){
                        $insertData = [
                          'rule_id' => $rule->id,
                           'file' =>  SettingFacade::get('ltbackup_dir','/backups').'/'.date('YmdHis').'.tar.gz'
                        ];
                         RunLog::query()->create($insertData);
                         //如果这个只执行一次
                         if($rule->period == 0){
                            $rule->status = 0;
                         }else{
                             $rule->next_run = \LTBackup\Extension\Facades\LTBackup::getNextRunTime($rule->time_at);
                         }
                         $rule->save();
                   });
               }
            }
            $isRunning =  RunLog::where('status',RunLog::RUN_STATE_RUNNING)->first();
            if($isRunning){
                $waitRun =  RunLog::where('status',RunLog::RUN_STATE_WAITING)->orderBy('created_at','asc')->first();
                if($waitRun){
                    $waitRun->status = RunLog::RUN_STATE_RUNNING;
                    $waitRun->running_at = date('Y-m-d H:i:s');
                    $waitRun->save();
                    $this->doJob($waitRun);
                }
            }else{   //看看有没有过期
                if(!$isRunning->running_at || ( (time() -  strtotime($isRunning->running_at)) / 60  >  SettingFacade::get('ltbackup_execute_timeout') )  ){
                    $waitRun->status = RunLog::RUN_STATE_FAIL;
                    $waitRun->save();
                    $this->runLog($log->id,'error:  执行超时！');
                    $this->run($all);
                }

            }

        }catch (\Exception $e){

        }
    }

    /**
     * 开始执行任务
     * @param RunLog $log
     */
    protected function doJob(RunLog $log){
        
        try{
            //先看下备份目录在不在,不存在则创建
            $backupDir = SettingFacade::get('ltbackup_dir','/backups');

            is_dir($backupDir.'/tmp') && delete_dir($backupDir.'/tmp') ;
            !is_dir($backupDir.'/tmp') && mkdir($backupDir.'/tmp',0777,true);

            //根据备份类型做相应的备份
            $this->runLog($log->id,'info: 开始执行备份...');
            $runType = $log->rule->type;
            $runTypeText = array_key_exists($runType,Rule::RULE_TYPE) ? Rule::RULE_TYPE[$runType] : '未知';
            $this->runLog($log->id,'info: 备份类型为：'. $runTypeText);

            switch ($runType){
                case 0 ://整站备份
                    break;
                case 1: //只备份源码
                    break;
                case 2: //只备份数据库
                    break;
                case -99://指定路径
                    break;
                default:
                    break;
            }
        }catch (\Exception $e){
            dd(11);
            $this->runLog($log->id,'error: '. $e->getMessage());
            $log->status = RunLog::RUN_STATE_FAIL;
            $log->save();
        }


    }


    private function mysqlDump($log){
        $this->runLog($log->id,'开始备份数据库...');
        
    }

    private function runLog($id,$log){
       file_put_contents(storage_path('logs/backup/'.$id.'_backup.log'),date('Y-m-d H:i:s').': '.$log."\n",8);
    }

    public function getNextRunTime($time_at){
        //否则则显示有效期内最接近的时间
        //看看当天是否已过
        if(strtotime(date('Y-m-d '. $time_at)) <  time() ){
            return date('Y-m-d '.$time_at ,strtotime('+1 day'));
        }else{
            return date('Y-m-d '.$time_at);
        }
    }
}