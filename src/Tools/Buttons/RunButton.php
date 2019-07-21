<?php

namespace LTBackup\Extension\Tools\Buttons;


use Encore\Admin\Grid\Tools\AbstractTool;
use LTBackup\Extension\Facades\Admin;

class RunButton extends AbstractTool
{

    protected $key;
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
        $this->key =  mt_rand() ;
    }
    protected function script()
    {

        $token = csrf_token();
        return <<<SCRIPT
        $('.btn-{$this->key}').on('click', function () {
            layer.open({
                  'type' : 0,
                    'title': '提示',
                    'content' : '立即运行该备份规则?',
                    'btn' : ['确认','取消'],
                    'yes' : function(){
                        layer.closeAll();
                  $.ajax({type: 'post',url:'ltbackup-log',data:{'_token':"{$token}",'rule_id':"{$this->id}"},dataType:'json',success: function(data){
                        layer.msg(data.message,{time:2000},function(){
                              $.pjax.reload('#pjax-container');
                        }); 
                 
                  },error: function(data){
                   layer.msg(data.message); 
                  }})
                    }
            });
        });
SCRIPT;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {
        Admin::script($this->script());
        return <<<EOT
<div class="btn-group " style="margin-right: 10px" >
    <a href="javascrit:void(0);" class=" btn-{$this->key}" title="执行" style="margin: 3px -2px 3px 3px;font-size:13px;">
        <i class="fa fa-hourglass-start" style="line-height:20px;">&nbsp;执行</i>
    </a>
</div>

EOT;
    }
}