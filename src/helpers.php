<?php
if(! function_exists('delete_dir')){
    function delete_dir($path){
            //如果是目录则继续
            if(is_dir($path)){
                //扫描一个文件夹内的所有文件夹和文件并返回数组
                $p = scandir($path);
                foreach($p as $val){
                    //排除目录中的.和..
                    if($val !="." && $val !=".."){
                        //如果是目录则递归子目录，继续操作
                        if(is_dir($path.$val)){
                            //子目录中操作删除文件夹和文件
                            deldir($path.$val.'/');
                            //目录清空后删除空文件夹
                            @rmdir($path.$val.'/');
                        }else{
                            //如果是文件直接删除
                            unlink($path.$val);
                        }
                    }
                }
            }

    }
}

if(! function_exists('has_disabled_functions')){
    function has_disabled_functions($functionName){
        $all = ini_get('disable_functions');
        $arrArr = explode(',',$all);
        foreach ($arrArr as $func){
            if($func == $functionName)
                return true;
        }
        return false;
    }
}

if (! function_exists('get_switch_state')) {
    function get_switch_state($type = 0)
    {
        $arr = [
            [
                'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
            ],
            [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
            ]
        ];
        return array_key_exists($type,$arr) ? $arr[$type] : $arr[0];
    }
}