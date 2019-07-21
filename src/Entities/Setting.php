<?php
namespace LTBackup\Extension\Entities;


use Illuminate\Database\Eloquent\Model;

/**
 * @author  wanghouting
 * @package LTBackup\Extension\Entities
 */
class Setting extends Model
{
    protected $fillable = ['name','cname','type','form', 'value', 'plainValue','extra','created_at','updated_at'];
    protected $table = 'ltbackup_settings';

}
