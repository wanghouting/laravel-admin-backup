<?php


namespace LTBackup\Extension\Tools\Form\Field;


use Encore\Admin\Form\Field\Html;
use Illuminate\Support\Arr;

class INotice extends Html
{
    protected  $html = [];

    protected $label = [];

    /**
     * Create a new Html instance.
     *
     * @param mixed $html
     * @param array $arguments
     */
    public function __construct($html, $arguments)
    {
        $this->html[] = $html;

        $this->label[] = Arr::get($arguments, 0);
    }


    public function next($html,$label)
    {
        $this->html[] = $html;

        $this->label[] = $label;
        return $this;
    }

    /**
     * @return array
     */
    public function getViewElementClasses()
    {
        $count  = count($this->label);
        $field_width = floor($this->width['field'] / $count);
        if ($this->horizontal) {
            return [
                'label'      => "col-sm-{$this->width['label']} {$this->getLabelClass()}",
                'field'      => "col-sm-{$field_width}",
                'form-group' => 'form-group ',
            ];
        }

        return ['label' => "{$this->getLabelClass()}", 'field' => '', 'form-group' => ''];
    }


    /**
     * Render html field.
     *
     * @return string
     */
    public function render()
    {
        $viewClass = $this->getViewElementClasses();
        $htmlContent = '<div class="form-group">';
        foreach ($this->html as $key => $html){
            if($html instanceof  \Closure){
                $html = $html->call($this->form->model(),$this->form);
            }
            $html = '<em><i class="fa fa-info-circle"> </i> '.$html.'</em>';
            $htmlContent .= ' <label  class="'.$viewClass['label'].' control-label">'.$this->label[$key].'</label> ';
            $htmlContent .= '<div   style="line-height: 32px;color: #555555;border:1px dashed pink;margin:0 15px;display: inline;padding: 6px;"> '.$html.' </div>';
        }
        $htmlContent .= '</div>';
        return $htmlContent;
    }
}