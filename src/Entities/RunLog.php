<?php


namespace LTBackup\Extension\Entities;


use Illuminate\Database\Eloquent\Model;

class RunLog extends Model
{
    protected $fillable = ['rule_id','status','file','running_at','created_at','updated_at'];
    protected $table = 'ltbackup_log';

    const RUN_STATE_WAITING = 0;
    const RUN_STATE_RUNNING = 1;
    const RUN_STATE_SUCCESS = 2;
    const RUN_STATE_FAIL = 3;
    const RUN_STATE_STOPED = 4;


    public function rule(){
        return $this->hasOne(Rule::class,'id','rule_id');
    }
}