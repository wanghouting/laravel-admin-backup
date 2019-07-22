<?php


namespace LTBackup\Extension\Entities;


use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['name','type','status','time_at','period','period_days','path','run_times','run_fail_times','created_at','updated_at'];
    protected $table = 'ltbackup_rule';

    const RULE_TYPE_ALL = 0;
    const RULE_TYPE_CODE = 1;
    const RULE_TYPE_DB = 2;
    const RULE_TYPE_DIR = -99;

    const RULE_TYPE = [
         self::RULE_TYPE_ALL=>'整站备份', self::RULE_TYPE_CODE =>'只备份源码',self::RULE_TYPE_DB =>'只备份数据库',self::RULE_TYPE_DIR =>'指定路径'
    ];

    const RULE_PERIOD = [
        -1 => '每天',0 => '一次',-99=>'指定天数'
    ];
}