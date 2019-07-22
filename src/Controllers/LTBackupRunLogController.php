<?php

namespace LTBackup\Extension\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\RunLog;
use LTBackup\Extension\Facades\DynamicOutput;
use LTBackup\Extension\Facades\LTBackup;
use LTBackup\Extension\Facades\SettingFacade;
use LTBackup\Extension\Support\DynamicOutputSupport;


/**
 * 备份控制器
 * Class LTBackupRunLogController
 * @author wanghouting
 */
class LTBackupRunLogController extends Controller
{
    public function store()
    {
        try{
            $rule_id = request()->get('rule_id',0);
            if(!$rule_id) throw new \Exception('参数错误');
            DB::transaction(function () use ($rule_id){
                $rule = Rule::find($rule_id);
                if($rule){
                    $rule->run_times += 1;
                    $rule->save();
                    $res =  RunLog::create(['rule_id'=>$rule_id,'file'=>$this->createFile($rule->type)]);
                    LTBackup::runLog($res->id,'队列等待中...');
                }
            });
          // LTBackup::run(false);
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
        $file = storage_path('logs/backup').'/'.$id.'_backup.log';
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
                while(!feof($fp))
                {
                    $lineStr =  fgets($fp);
                    $readCount +=  strlen($lineStr);
                    DynamicOutput::addClause($lineStr, DynamicOutputSupport::TYPE_MESSAGE);
                    DynamicOutput::output(false,false,true);

                }
                fclose($fp);
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
        return response()->json(['code'=>200,'message'=>'获取成功','data'=>$res]);
    }

    private function createFile($type){
        $filename = SettingFacade::get('ltbackup_dir','/var/data/backup') .'/'.date('Ymd').'/' .date('YmdHis');
        switch ($type){
            case Rule::RULE_TYPE_ALL:
                $filename .= '_all.tar.gz';
                break;
            case Rule::RULE_TYPE_CODE:
                $filename .= '_code.tar.gz';
                break;
            case Rule::RULE_TYPE_DB:
                $filename .= '_db.sql.gz';
                break;
            case Rule::RULE_TYPE_DIR:
                $filename .= '_dir.tar.gz';
                break;
            default:
                $filename .= '.tar.gz';
                break;
        }
       return $filename ;
    }
}