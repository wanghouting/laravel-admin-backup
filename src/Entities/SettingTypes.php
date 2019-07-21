<?php

namespace LTBackup\Extension\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  wanghouting
 * @package LTBackup\Extension\Entities
 */
class SettingTypes extends Model
{
    protected $fillable = ['name','sort','is_show','module','created_at','updated_at'];
    protected $table = 'ltbackup_setting_types';
}
