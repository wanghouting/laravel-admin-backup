<?php


namespace LTBackup\Extension\Tools\Form\Field;


use Encore\Admin\Form\Field\Number;
use Encore\Admin\Form\Field\Text;
use Illuminate\Contracts\Support\Arrayable;
use LTBackup\Extension\Tools\Form\Builder;
use LTBackup\Extension\Tools\Form\Field;
use LTBackup\Extension\Tools\Form\Form;


class IRadio extends Field
{
    protected $inline = true;
    protected $lastWithInput = [];
    protected $id;
    protected static $css = [
        '/vendor/laravel-admin/AdminLTE/plugins/iCheck/all.css',
    ];

    protected static $js = [
        'vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js',

    ];

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }
        $this->id = 'id_'.mt_rand(10000,99999);
        $this->options = (array) $options;
        $this->view = 'laravel-admin-backup::form.radio';
        //$this->setView('laravel-admin-backup::form.radio');
        return $this;
    }

    /**
     * @param Form $form
     * @param $column
     * @param string $type
     * @param array $options
     * @param string $placeholder
     * @return $this
     */

    public function lastInput(Form $form, $column,$type='text', $options = [], $placeholder = '')
    {
        switch ($type){
            case 'number':
                $form->addField((new Number($column)));
                break;
             default:
                 $form->addField(new text($column));
                 break;
        }
        $this->lastWithInput['column'] = $column;
        $this->lastWithInput['type'] = $type;
        $this->lastWithInput['style'] = isset($options['style']) ? $options['style'] : '';
        $this->lastWithInput['min'] = isset($options['min']) ? $options['min'] : 0;
        $this->lastWithInput['max'] = isset($options['max']) ? $options['max'] : 1000000;
        $this->lastWithInput['value'] = isset($options['value']) ? $options['value'] : ( isset($options['default']) ? $options['default'] : ( $type== 'text' ? '' : 0));
        $this->lastWithInput['placeholder'] = $placeholder;
        $this->lastWithInput['id'] = 'input_id_'.mt_rand(10000,99999);
        return $this;
    }

    /**
     * Set checked.
     *
     * @param array|callable|string $checked
     *
     * @return $this
     */
    public function checked($checked = [])
    {
        if ($checked instanceof Arrayable) {
            $checked = $checked->toArray();
        }

        // input radio checked should be unique
        $this->checked = is_array($checked) ? (array) end($checked) : (array) $checked;

        return $this;
    }


    /**
     * Draw inline radios.
     *
     * @return $this
     */
    public function inline()
    {
        $this->inline = true;

        return $this;
    }

    /**
     * Draw stacked radios.
     *
     * @return $this
     */
    public function stacked()
    {
        $this->inline = false;

        return $this;
    }

    /**
     * Set options.
     *
     * @param array|callable|string $values
     *
     * @return $this
     */
    public function values($values)
    {
        return $this->options($values);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->script = <<<EOT
        $('{$this->getElementClassSelector()}').iCheck({radioClass:'iradio_minimal-blue'});
        var doClick = function(){ 
            $('#{$this->lastWithInput['id']}').css({'display':'inline-block'})
        };
        var unClick = function(){ 
            $('#{$this->lastWithInput['id']}').css({'display':'none'})
        }; 
        if($('#{$this->id}').length > 0){
             if($('#{$this->id}').parent('.checked').length > 0){
                $('#{$this->lastWithInput['id']}').css({'display':'inline-block'})
             }
            $('input{$this->getElementClassSelector()}').each(function(){
                  if($(this).parent().siblings('.extra-input-text').length == 0){
                      $(this).siblings('.iCheck-helper').click(unClick);
                      $(this).parent().parent().click(unClick);
                  }else{
                      $(this).siblings('.iCheck-helper').click(doClick);
                      $(this).parent().parent().click(doClick);
                  }
            })
        };
EOT;
        $this->addVariables(['options' => $this->options, 'checked' => $this->checked, 'inline' => $this->inline,'lastInput'=>$this->lastWithInput,'optionsCount'=>count($this->options),'id'=>$this->id]);

        return parent::render();
    }


}