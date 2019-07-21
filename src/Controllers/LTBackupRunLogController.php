<?php

namespace LTBackup\Extension\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\RunLog;
use LTBackup\Extension\Facades\SettingFacade;


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
                    RunLog::create(['rule_id'=>$rule_id,'file'=> SettingFacade::get('ltbackup_dir','/var/data/backup').'/'.date('YmdHis').'.tar.gz']);
                }
            });
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
}