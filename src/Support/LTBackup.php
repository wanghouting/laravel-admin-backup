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
            case 0:
                return '<label class="label label-primary"><i class="fa fa-spinner fa-pulse "></i> 正在等待</label>';
            case 1:
                return '<label class="label label-info"><i class="fa fa-spinner fa-pulse "></i> 正在执行</label>';
            case 2:
                return '<label class="label label-success"><i class="fa fa-spinner fa-pulse "></i> 执行完成</label>';
            case 3:
                return '<label class="label label-danger"><i class="fa fa-spinner fa-pulse "></i> 执行失败</label>';
            case 4:
                return '<label class="label label-warning"><i class="fa fa-spinner fa-pulse "></i> 用户停止</label>';
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

            $isRunning =  RunLog::where('status',1)->count();
            if($isRunning == 0){
                $waitRun =  RunLog::where('status',0)->orderBy('created_at','asc')->first();
                if($waitRun){
//                    $waitRun->status = 1;
//                    $waitRun->save();
                    $this->doJob($waitRun);
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

//            if(strtoupper(substr(PHP_OS,0,3))==='WIN'){
//
//            }else{
//                exec(__DIR__.'/../Shell/linux/linux_dir.sh '.$backupDir,$res);
//                if($res[0] !== 'ok'){
//                    $this->runLog($log->id,'error: '.$res[0]);
//                }else{
//                    $this->runLog($log->id,'info: '.$res[0]);
//                }
//            }

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
            $this->runLog($log->id,'error: '. $e->getMessage());

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