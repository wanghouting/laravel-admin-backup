<?php


namespace LTBackup\Extension\Entities;


use Illuminate\Database\Eloquent\Model;

class RunLog extends Model
{
    protected $fillable = ['rule_id','status','ftp_status','run_type','file','ftp_file','filesize','running_at','created_at','updated_at'];
    protected $table = 'ltbackup_log';

    const RUN_STATE_WAITING = 0;
    const RUN_STATE_RUNNING = 1;
    const RUN_STATE_SUCCESS = 2;
    const RUN_STATE_FAIL = 3;
    const RUN_STATE_STOPED = 4;

    const FTP_STATUS_UNKNOW = 0;
    const FTP_STATUS_NO_OPEN = 1;
    const FTP_STATUS_SUCCESS = 2;
    const FTP_STATUS_FAIL = 3;
    const FTP_STATUS_STOPED = 4;

    const RUN_TYPE_AUTO = 0;
    const RUN_TYPE_MANUAL = 1;
    public function rule(){
        return $this->hasOne(Rule::class,'id','rule_id');
    }
}