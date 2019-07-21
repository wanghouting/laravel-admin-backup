<?php


namespace LTBackup\Extension\Tools\Form;


use Encore\Admin\Form\Tools;
use Illuminate\Support\Collection;

class Builder extends \Encore\Admin\Form\Builder
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * Builder constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->fields = new Collection();

        $this->init();
    }


    public function addField($name)
    {
        $this->fields[] = $name;

    }

    /**
     * Do initialize.
     */
    public function init()
    {
        $this->tools = new Tools($this);
        $this->footer = new Footer($this);
    }

    /**
     * @return \Encore\Admin\Form\Footer
     */
    public function getFooter()
    {
        return $this->footer;
    }

    public function getForm()
    {
        return $this->form;
    }
}