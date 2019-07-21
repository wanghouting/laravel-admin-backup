<?php

namespace LTBackup\Extension\Tools\Grid;


use Encore\Admin\Facades\Admin;

/**
 * Class Actions
 * @author wanghouting
 * @package Modules\Admin\Tools\Grid
 */
class Actions extends  \Encore\Admin\Grid\Displayers\Actions {

    protected $actions = ['view','edit', 'delete'];

    /**
     * Render view action.
     *
     * @return string
     */
    protected function renderView()
    {
        return <<<EOT
<a href="{$this->getResource()}/{$this->getKey()}" style="margin: 3px;">
    <i class="fa fa-eye">&nbsp;查看</i>
</a>
EOT;
    }

    /**
     * Render edit action.
     *
     * @return string
     */
    protected function renderEdit()
    {
        return <<<EOT
<a href="{$this->getResource()}/{$this->getKey()}/edit"  style="margin: 3px;">
    <i class="fa fa-edit">&nbsp;编辑</i>
</a>
EOT;
    }

    /**
     * Render delete action.
     *
     * @return string
     */
    protected function renderDelete()
    {
        $deleteConfirm = trans('admin.delete_confirm');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        $script = <<<SCRIPT

$('.{$this->grid->getGridRowName()}-delete').unbind('click').click(function() {

    var id = $(this).data('id');

    swal({
        title: "$deleteConfirm",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "$confirm",
        showLoaderOnConfirm: true,
        cancelButtonText: "$cancel",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '{$this->getResource()}/' + id,
                    data: {
                        _method:'delete',
                        _token:LA.token,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

SCRIPT;

        Admin::script($script);

        return <<<EOT
<a href="javascript:void(0);" data-id="{$this->getKey()}" class="{$this->grid->getGridRowName()}-delete" style="margin: 3px;">
    <i class="fa fa-trash">&nbsp;删除</i>
</a>
EOT;
    }
}
