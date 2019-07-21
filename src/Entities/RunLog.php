<?php


namespace LTBackup\Extension\Entities;


use Illuminate\Database\Eloquent\Model;

class RunLog extends Model
{
    protected $fillable = ['rule_id','status','file','created_at','updated_at'];
    protected $table = 'ltbackup_log';

    const RUN_STATE = [
        0=>'等待中',1=>'运行中',2=>'成功',3=>'失败',4=>'停止'
    ];

    public function rule(){
        return $this->hasOne(Rule::class,'id','rule_id');
    }
}