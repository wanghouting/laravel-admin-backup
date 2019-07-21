<?php


namespace LTBackup\Extension\Entities;


use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['name','type','status','time_at','period','period_days','path','run_times','run_fail_times','created_at','updated_at'];
    protected $table = 'ltbackup_rule';

    const RULE_TYPE = [
        0=>'整站备份',1=>'只备份源码',2=>'只备份数据库',-99=>'指定路径'
    ];

    const RULE_PERIOD = [
        -1 => '每天',0 => '一次',-99=>'指定天数'
    ];
}