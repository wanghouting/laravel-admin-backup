<?php
namespace LTBackup\Extension\Support;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\RunLog;
use LTBackup\Extension\Facades\SettingFacade;
use LTBackup\Extension\Facades\WebConsole;

/**
 * @author wanghouting
 * Class LTBackup
 */
class LTBackup{
    const LOG_END ='@@++end++@@';

    private $supportDriver = [
        'mysql'
    ];

    public function getRunStateLabel($state,$id)
    {
        switch ($state){
            case RunLog::RUN_STATE_WAITING:
                return '<label class="label label-primary" data-id="'.$id.'" data-value="'.$state.'"><i class="fa fa-spinner fa-pulse "></i> 正在等待</label>';
            case RunLog::RUN_STATE_RUNNING:
                return '<label class="label label-info"  data-id="'.$id.'" data-value="'.$state.'"><i class="fa fa-spinner fa-pulse "></i> 正在执行</label>';
            case RunLog::RUN_STATE_SUCCESS:
                return '<label class="label label-success" data-id="'.$id.'" data-value="'.$state.'" > 执行完成</label>';
            case RunLog::RUN_STATE_FAIL:
                return '<label class="label label-danger" data-id="'.$id.'" data-value="'.$state.'"> 执行失败</label>';
            case RunLog::RUN_STATE_STOPED:
                return '<label class="label label-warning" data-id="'.$id.'" data-value="'.$state.'"> 用户停止</label>';
            default:
                return '<label class="label label-warning" data-id="'.$id.'" data-value="'.$state.'"> 未知</label>';
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
            if(!$isRunning){
                $waitRun =  RunLog::where('status',RunLog::RUN_STATE_WAITING)->orderBy('created_at','asc')->first();
                if($waitRun){
                    $waitRun->status = RunLog::RUN_STATE_RUNNING;
                    $waitRun->running_at = date('Y-m-d H:i:s');
                    $waitRun->save();
                    $this->doJob($waitRun);
                }
            }else{   //看看有没有过期
                if(!$isRunning->running_at || (  floor((time() -  strtotime($isRunning->running_at)) / 60 ) >  SettingFacade::get('ltbackup_execute_timeout') )  ){
                    $this->failed($isRunning,'执行超时！');
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
            $filePathArr = explode('/',$log->file);
            unset($filePathArr[count($filePathArr ) -1]);
            $filePath =   implode('/',$filePathArr);

            if(!is_dir($filePath)) @mkdir($filePath,0777,true);

            //根据备份类型做相应的备份
            $this->runLog($log->id,'info: 开始执行备份...');
            $runType = $log->rule->type;
            $runTypeText = array_key_exists($runType,Rule::RULE_TYPE) ? Rule::RULE_TYPE[$runType] : '未知';
            $this->runLog($log->id,'info: 备份类型为：'. $runTypeText);

            switch ($runType){
                case Rule::RULE_TYPE_ALL ://整站备份
                    $this->backupAll($log,$runType,$filePath);
                    break;
                case Rule::RULE_TYPE_CODE: //只备份源码
                    $this->backupDir($log,$runType);
                    break;
                case Rule::RULE_TYPE_DB: //只备份数据库
                    $this->backupDb($log);
                    break;
                case Rule::RULE_TYPE_DIR://指定路径
                    $this->backupDir($log,$runType);
                    break;
                default:
                    break;
            }
            $this->success($log,'执行成功!');
        }catch (\Exception $e){
            $this->failed($log,$e->getMessage());
        }
    }

    private function clearTmpDir($backupDir){
        is_dir($backupDir) && delete_dir($backupDir);
        @mkdir($backupDir,0777,true);
    }

    private function isDbDriverSupport($driver){
        return in_array($driver,$this->supportDriver);
    }

    private function getDbDriver(){
        return trim(config('database.default')) ;
    }

    private function backupAll(RunLog $log,$runType,$filePath){
        $this->backupDir($log,$runType,$filePath);
        $this->backupDb($log,$filePath);
        $command = 'tar -zcf '. $log->file . ' -C '  .$filePath .' db site ' ;
        $this->executeCommand($log,$command);
    }

    private function backupDb(RunLog $log,$filePath = ''){

        $dbDriver = $this->getDbDriver();

        if( ! $this->isDbDriverSupport($dbDriver)){
            throw new \Exception('暂时只支持以下类型的数据库：'.implode(' ',$this->supportDb));
        }
        $dbConfig =  config('database.connections.'.$dbDriver);
        $database = $dbConfig['database'];
        if(empty($database)) throw new \Exception('请先配置数据库');
        $command = '';
        $sqlFile = $log->file;
        if(!empty($filePath)){
            $realPath = $filePath.'/db/';
            $this->clearTmpDir($realPath);
            $sqlFile =  $realPath .$database.'.sql.gz';
        }
        switch ($dbDriver){
            case 'mysql':
                $mysqldumpPath =  $this->findMysqldump();
                $this->runLog($log->id,'info: 找到mysqldump路径：'.$mysqldumpPath);
                $command = $mysqldumpPath .' -h'.$dbConfig['host'] . ' -P'.$dbConfig['port'];
                $command .= ' -u'.$dbConfig['username'] .' -p' .  $dbConfig['password'];
                $command .= ' '.$database .' | gzip >  '. $sqlFile;

                break;
            default:
                break;
        }
        $this->runLog($log->id,'info: 开始备份数据库'.$database);
        $this->executeCommand($log,$command);
        $this->runLog($log->id,'info: 备份数据库'.$database.'完成！');

    }

    private function findMysqldump(){
        try{
            $command = "whereis mysqldump |  awk '{print $2}'";
            $res = WebConsole::execute_command($command);
            return trim($res['output']);
        }catch (\Exception $e){
            throw  new  \Exception('找不到mysqldump 路径，请确保已安装mysqlclient,并且将mysqldump加到环境变量！');
        }

    }

    /**
     * 拆分目录
     * @param $dir
     * @return array
     */
    private function exploadDir($dir){
        $dirArr = explode('/',$dir);
        $dirCount = count($dirArr);
        $childDir = $dirArr[$dirCount -1];
        unset($dirArr[$dirCount -1]);
        $parentDir = implode('/',$dirArr);
        return [$parentDir,$childDir];
    }

    /**
     * 打包指定目录
     * @param RunLog $log
     * @param $type
     */
    private function backupDir(RunLog $log,$type,$filePath = ''){

        $rule = $log->rule;
        $dir =  $type == Rule::RULE_TYPE_DIR ?  $rule->path : base_path();
        if(empty($dir)  || !is_dir($dir)){
            throw new \Exception('备份的目录:'.$dir.'不存在');
        }
        $this->runLog($log->id,'info: 开始备份目录'.$dir);
        list($parentDir, $childDir) = $this->exploadDir($dir);
        if(!empty($filePath)){
            $realPath = $filePath.'/site/';
            $this->clearTmpDir($realPath);
            $command = 'tar -zcf '.$realPath.$childDir.'.tar.gz' . ' -C '  .$parentDir .' ' .$childDir ;
        }else{

            $command = 'tar -zcf '. $log->file . ' -C '  .$parentDir .' ' .$childDir ;
        }
        $this->runLog($log->id,'info: '.$command);
        $this->executeCommand($log,$command);
        $this->runLog($log->id,'info: 备份目录'.$dir.'完成！');
    }

    private function executeCommand($log,$command){
        $this->runLog($log->id,$command);
        $res = WebConsole::execute_command($command);
        $this->runLog($log->id,$res['output']);
    }

    /**
     * 执行成功
     * @param RunLog $log
     * @param $message
     */
    private function success(RunLog $log, $message){
        $log->status = RunLog::RUN_STATE_SUCCESS;
        $log->save();
        $this->runLog($log->id, 'info: ' . $message);
        $this->runLog($log->id,self::LOG_END);
        $this->run(true);
    }

    /**
     * 执行失败处理
     * @param RunLog $log
     * @param $message
     */
    private function failed(RunLog $log, $message){
        DB::transaction(function () use ($log,$message) {
            $log->status = RunLog::RUN_STATE_FAIL;
            $log->save();
            $rule = $log->rule;
            $rule->run_fail_times += 1;
            $rule->save();
        });
        $this->runLog($log->id, 'error: ' . $message);
        $this->runLog($log->id,self::LOG_END);
        $this->run(true);
    }
    private function mysqlDump($log){
        $this->runLog($log->id,'开始备份数据库...');

    }

    public function runLog($id,$log){
        file_put_contents(storage_path('logs/backup/'.$id.'_backup.log'),$log."\n",8);
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