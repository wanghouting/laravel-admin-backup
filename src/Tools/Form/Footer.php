<?php


namespace LTBackup\Extension\Tools\Form;


use Encore\Admin\Admin;

class Footer extends \Encore\Admin\Form\Footer
{
    protected $isCheckContinueEdit = false;
    protected $isCheckContinueShow = false;
    protected $isCheckContinueAdd = false;

    protected function _checkContinueAdd(){
        $script = <<<'EOT'
$('.after-submit[value="2"]').parent().click();
EOT;
        Admin::script($script);
    }

    public function checkContinueAdd(){
        $this->isCheckContinueAdd = true;
    }

    protected function _checkContinueEdit(){
        $script = <<<'EOT'
$('.after-submit[value="1"]').parent().click();
EOT;
        Admin::script($script);
    }

    public function checkContinueEdit(){
        $this->isCheckContinueEdit = true;
    }

    protected function _checkContinueShow(){
        $script = <<<'EOT'
$('.after-submit[value="3"]').parent().click();
EOT;
        Admin::script($script);
    }

    public function checkContinueShow(){
        $this->isCheckContinueShow = true;
    }

    /**
     * @return array|string
     * @throws \Throwable
     */
    public function render()
    {
        $this->setupScript();
        if($this->isCheckContinueEdit) $this->_checkContinueEdit();
        if($this->isCheckContinueShow) $this->_checkContinueShow();
        if($this->isCheckContinueAdd) $this->_checkContinueAdd();

        $data = [
            'buttons'      => $this->buttons,
            'checkboxes'   => $this->checkboxes,
            'width'        => $this->builder->getWidth(),
        ];

        return view($this->view, $data)->render();
    }

}