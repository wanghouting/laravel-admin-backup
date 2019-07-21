<?php
namespace LTBackup\Extension\Databases\Seeders;

use Encore\Admin\Auth\Database\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use LTBackup\Extension\Entities\InstallLog;
use LTBackup\Extension\Entities\Rule;
use LTBackup\Extension\Entities\SettingTypes;
use LTBackup\Extension\Entities\Setting;

class LTBackupDatabaseSeeder extends Seeder
{


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        if($menu = InstallLog::find(1)){
            Menu::where('id',$menu->menu_id)->orWhere('parent_id',$menu->menu_id)->delete();
        }

        $date = date("Y-m-d H:i:s");
        $TopMenus = [
            'parent_id' => 0,
            'order'     => 1,
            'title'     => '数据备份',
            'icon'      => 'fa-database',
            'uri'       => '/',
            'created_at' => $date,
            'updated_at' => $date
        ];
        $parentId = Menu::insertGetId($TopMenus);
        InstallLog::truncate();
        InstallLog::create(['menu_id'=>$parentId]);

        $subMenus = [

            [
                'parent_id' => $parentId,
                'order'     => 3,
                'title'     => '备份规则',
                'icon'      => 'fa-check',
                'uri'       => 'ltbackup-rule',
            ],
            [
                'parent_id' => $parentId,
                'order'     => 3,
                'title'     => '备份设置',
                'icon'      => 'fa-cog',
                'uri'       => 'ltbackup-setting',
            ],
        ];
        foreach ($subMenus as &$menu){
            $menu['updated_at'] = $date;
            $menu['created_at'] = $date;
        }
        Menu::insert($subMenus);

        SettingTypes::truncate();
        $types = [
            [
                'id'            =>  1,
                'sort'         =>  1,
                'name'          =>  '基本设置',
                'module'        =>  'backup',
            ],
            [
                'id'            =>  2,
                'sort'         =>  1,
                'name'          =>  '百度云盘',
                'module'        =>  'backup',
            ],
        ];

        foreach ($types as &$type){
            $type['updated_at'] = $type['created_at'] = date('Y-m-d H:i:s');
        }

        SettingTypes::insert($types);

        Setting::truncate();

        $settings = [
            [
                'type'        => 1,
                'name'        => 'ltbackup_status',
                'cname'       => '备份功能',
                'form'        => 'switch',
                'plainValue'  => 'on',
                'extra'       =>  json_encode(['help'=>'打开或关闭备份功能']),
            ],
            [
                'type'        => 1,
                'name'        => 'ltbackup_dir',
                'cname'       => '备份存储目录',
                'form'        => 'text',
                'plainValue'  => '/backups',
                'extra'       =>  json_encode(['help'=>'备份文件存储目录，如果目录不存在，则自动创建']),
            ],
            [
                'type'        => 1,
                'name'        => 'ltbackup_local',
                'cname'       => '服务器保留备份',
                'form'        => 'switch',
                'plainValue'  => 'on',
                'extra'       =>  json_encode(['help'=>'服务器本地是否需要保留备份文件']),
            ],

            [
                'type'        => 1,
                'name'        => 'ltbackup_delete',
                'cname'       => '自动删除备份文件',
                'form'        => 'switch',
                'plainValue'  => 'on',
                'extra'       =>  json_encode(['help'=>'打开此功能将自动删除过期的备份文件']),
            ],
            [
                'type'        => 1,
                'name'        => 'ltbackup_date',
                'cname'       => '备份文件保留天数',
                'form'        => 'number',
                'plainValue'  => '7',
                'extra'       =>  json_encode(['min'=>1,'max'=>10000]),
            ],
            [
                'type'        => 1,
                'name'        => 'ltbackup_to_baiduyun',
                'cname'       => '备份上传百度云盘',
                'form'        => 'switch',
                'plainValue'  => 'on',
                'extra'       =>  json_encode(['help'=>'使用此功能请先配置百度云盘']),
            ],
        ];
        foreach ($settings as &$setting){
            $setting['updated_at'] = $setting['created_at'] = date('Y-m-d H:i:s');
        }
        Setting::insert($settings);

        Rule::truncate();

        $rules = [
            [
                'name' => '全站备份',
                'type' => 0,
                'status' => 1,
                'time_at' => '01:00',
                'period' => -1,
                'path' => '',
            ],
            [
                'name' => '备份源码',
                'type' => 1,
                'status' => 0,
                'time_at' => '01:00',
                'period' => 0,
                'path' => '',
            ],
            [
                'name' => '备份数据库',
                'type' => 2,
                'status' => 0,
                'time_at' => '01:00',
                'period' => 7,
                'path' => '',
            ],
            [
                'name' => '备份上传文件',
                'type' => 3,
                'status' => 0,
                'time_at' => '01:00',
                'period' => 7,
                'path' => public_path(),
            ],
        ];

        foreach ($rules as &$rule){
            $rule['updated_at'] = $rule['created_at'] = date('Y-m-d H:i:s');
        }
        Rule::insert($rules);
    }

}
