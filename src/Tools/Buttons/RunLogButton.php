<?php

namespace LTBackup\Extension\Tools\Buttons;


use Encore\Admin\Grid\Tools\AbstractTool;
use LTBackup\Extension\Facades\Admin;

class RunLogButton extends AbstractTool
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
                                  type: 2,
                                  skin: 'layui-layer-rim-blank', //加上边框
                                  area: ['640px', '420px'], //宽高
                                  content: '/admin/ltbackup-log-view?id=' + {$this->id}
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
    <a href="javascrit:void(0);" class=" btn-{$this->key}" title="日志" style="margin: 3px -2px 3px 3px;font-size:13px;">
        <i class="fa fa-file-word-o" style="line-height:20px;">&nbsp;日志</i>
    </a>
</div>

EOT;
    }
}