<?php

namespace LTBackup\Extension\Support;

use Closure;
use Encore\Admin\Form\Tools;
use LTBackup\Extension\Facades\Admin;
use LTBackup\Extension\Tools\Form\Form;

/**
 * Class FormViewBuilder
 * @author wanghouting
 * @package LTBackup\Extension\Support
 */
class FormViewBuilder {
    /**
     * @var Form;
     */
    protected $form = null;

    /**
     * 创建表单
     * @param $model
     * @param Closure $callback
     * @return FormViewBuilder
     * @deprecated  please use  LTBackup\Extension\Facades\FormBuild::buildFrom
     */
    public  function buildFrom($model, Closure $callback) {
        $this->form = Admin::form($model,$callback);
        return $this;
    }



    /**
     * 设置表单底部
     * @param bool $showViewCheck
     * @param bool $showEditCheck
     * @param bool $showCreateCheck
     * @param bool $showReset
     * @param bool $showSubmit
     * @return $this|bool
     */
    public function footer(bool $showViewCheck = false, bool $showEditCheck = false, bool $showCreateCheck = false,bool $showReset = true, bool $showSubmit = true) {
        if(!$this->form) return false;
        $this->form->footer(function ($footer) use ($showViewCheck,$showEditCheck,$showCreateCheck,$showReset,$showSubmit) {
            // 去掉`查看`checkbox
            !$showViewCheck &&  $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
            !$showEditCheck && $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            !$showCreateCheck && $footer->disableCreatingCheck();
            // 去掉`重置`按钮
            !$showReset && $footer->disableReset();
            // 去掉`提交`按钮
            !$showSubmit && $footer->disableSubmit();
        });
        return $this;
    }

    /**
     * 设置表单工具栏
     * @param bool $showList
     * @param bool $showDelete
     * @param bool $showView
     * @return $this|bool
     */
    public  function tools(bool $showList = false, bool $showDelete = false ,bool $showView = false) {
        if(!$this->form) return false;
        $this->form->tools(function (Tools $tools) use ($showList, $showDelete, $showView) {
            // 去掉`列表`按钮
            !$showList && $tools->disableList();
            // 去掉`删除`按钮
            !$showDelete && $tools->disableDelete();
            // 去掉`查看`按钮
            !$showView && $tools->disableView();
        });
        return $this;
    }

    /**
     * 设置标题
     * @param string $title
     * @return $this|bool
     */
    public function setTitle($title) {
        if(!$this->form) return false;
        $this->form->setTitle($title);
        return $this;
    }

    /**
     * 设置uri
     * @param $action
     * @return $this|bool
     */
    public function setAction($action) {
        if(!$this->form) return false;
        $this->form->setAction($action);
        return $this;
    }

    /**
     * 返回表单
     * @return Form
     */
    public function get() {
        return $this->form;
    }
}
