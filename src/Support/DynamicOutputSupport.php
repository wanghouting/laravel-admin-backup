<?php
/**
 * Created by PhpStorm.
 * User: wanghouting
 * Date: 2019-07-21
 * Time: 15:02
 */

namespace LTBackup\Extension\Support;

use LTBackup\Extension\Exceptions\WebConsoleException;
use LTBackup\Extension\Facades\WebConsole;

/**
 * Class DynamicOutputSupport
 * @author wanghouting
 * @package LTBackup\Extension\Support
 */
class DynamicOutputSupport {
    //条目类型， 1表示直接输出的文本，2表示要执行的命令输出
    const TYPE_MESSAGE = 1;
    const TYPE_COMMAND = 2;

    private $clauses = [];

    /**
     * 添加输出条目
     * @param $content
     * @param $type
     */
    public function addClause($content,$type){
        $this->clauses [] = ['content' => $content, 'type' => $type] ;
    }

    /**
     * 获取输出条目
     * @return array
     */
    public function getClause() {
        return $this->clauses;
    }

    /**
     * 开始执行动态输出
     */
    public function output($init = true ,$end = true , $sleep = true) {
        if($init)
            $this->initBody();
        foreach ($this->clauses as $clause ){
            if(!$this->doOutput($clause,$sleep)) break;
        }
        $this->clauses = [];
        if($end)
            $this->endBody();
    }

    /**
     * 动态输出处理
     * @param array $clause
     * @return bool
     */
    private function doOutput(array $clause,$sleep = true){
        try{
            $this->ssPrint($clause['content'],false,$sleep);
            switch ($clause['type']){
                case self::TYPE_MESSAGE:
                    break;
                case self::TYPE_COMMAND:
                    $res =  WebConsole::execute_command($clause['content']);
                    $trimContent = trim($clause['content']);
                    if(strtolower(substr($trimContent,0,2))  === 'cd' ){
                        @chdir(substr($trimContent,2,-1));
                    }
                    $this->ssPrint($res['output'],false,$sleep);
                    break;
                default:
                    break;
            }
            return true;
        }catch (WebConsoleException $e){
            $this->ssPrint("error: ".$e->getMessage(),true);
            $this->ssPrint("stopped",true);
            return false;
        }
    }


    /**
     * 初始化输出弹出层
     */
    private function initBody(){
        ob_end_clean();
        set_time_limit(0);
        ob_implicit_flush();
        header('X-Accel-Buffering: no');
        echo "<style> body {background-color: #0C0C0C} </style>";
        echo "<div style='width:100%;height: auto;background-color: #0C0C0C;margin: 10px 0;'>";
        echo '<script type="text/javascript" src="/vendor/laravel-admin-backup/jquery/jquery.min.js"></script>';
        echo "<script>var scroll = function(){ $('body').scrollTop(1000000);}; </script>";
    }

    /**
     * 格式化输出
     * @param $message
     * @param bool $isError
     */
    protected function ssPrint($message,$isError = false,$sleep = true){
        if(empty(trim($message))) return;
        $color = $isError ? 'red' : 'floralwhite';
        echo  "<span style='color: ".$color.";line-height: 20px;font-size: 13px;'>&nbsp;&nbsp;&nbsp;&nbsp;".date("Y-m-d H:i:s").': '. $message."</span></br>".$this->getScroll();
        if($sleep)
            usleep(500000);
    }

    /**
     * 结束标签
     */
    private  function endBody(){
        echo "</div>";
    }

    /**
     * 获取scroll ,实现滚动条自动滚动
     * @return string
     *
     */
    private function getScroll(){
        return  "<script> scroll() </script>";
    }

}