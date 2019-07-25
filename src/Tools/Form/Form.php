<?php

namespace LTBackup\Extension\Tools\Form;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LTBackup\Extension\Tools\Form\Field\IButton;
use LTBackup\Extension\Tools\Form\Field\IHidden;
use LTBackup\Extension\Tools\Form\Field\IHtml;
use LTBackup\Extension\Tools\Form\Field\INotice;
use LTBackup\Extension\Tools\Form\Field\IRadio;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Form
 * @author wanghouting
 * @method  Model model();
 * @method Field\IHtml           iHtml($html, $label = '')
 * @method Field\INotice          iNotice($html, $label = '')
 * @method Field\IRadio          iRadio($html, $label = '')
 * @method Field\IHidden          iHidden($html, $label = '')
 * @package LTBackup\Extension\Tools\Form
 */
class Form extends \Encore\Admin\Form {

    private $timestamps = true;

    protected $extra_field = [];
    /**
     * @var Builder
     */
    protected $builder;

    public function __construct($model, Closure $callback = null) {
        //增加iHtml类型
        static::$availableFields['iHtml'] = IHtml::class;
        static::$availableFields['iNotice'] = INotice::class;
        static::$availableFields['iRadio'] = IRadio::class;
        static::$availableFields['iHidden'] = IHidden::class;
        static::$availableFields['button'] = IButton::class;
        $this->model = $model;
        $this->builder = new Builder($this);
        if ($callback instanceof Closure) {
            $callback($this);
        }

        $this->isSoftDeletes = in_array(SoftDeletes::class, class_uses_deep($this->model));

        $this->callInitCallbacks();
        $this->iTimeStamps();
    }


    /**
     * @return Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    public function addField(\Encore\Admin\Form\Field $field){
        $this->extra_field[] = $field;
    }


    /**
     * Footer setting for form.
     *
     * @param Closure $callback
     */
    public function footer(Closure $callback = null)
    {
        if (func_num_args() == 0) {
            return $this->builder()->getFooter();
        }

        call_user_func($callback, $this->builder()->getFooter());
    }
    /**
     * 排序
     * @param $column
     * @param $label
     */
    public  function iSort($column, $label) {
        $this->number($column,$label)->default(50)->min(0)->max(99);
    }

    /**
     * 自定义的switch
     * @param $column
     * @param $label
     * @param $type
     * @param int $default
     * @return $this
     */

    public function iSwitch($column, $label,$type = 0,$default = 1) {
        $default = is_numeric($default) ? $default : ($default == 'on' ?  1 : 0);
        return $this->switch($column,$label)->states(get_switch_state($type))->default($default);

    }

    /**
     * 自定义的必填的、唯一的Text字段
     * @param $column
     * @param $label
     * @param array $extraRules
     * @param array $extraMessages
     */
    public function iRequiredUniqueText($column, $label,array $extraRules = [],array $extraMessages = []){
        $this->text($column,$label)->rules(function($form) use ($column,$extraRules){
            return  array_merge([
                'required',
                'unique:'.$form->model()->getTable().','.$column.',' .$form->model()->id
            ],$extraRules);
        },array_merge([
            'required'=> '请填写'.$label,
            'unique' => $label.'已存在'
        ],$extraMessages));
    }
    /**
     * Handle update.
     *
     * @param int  $id
     * @param null $data
     *
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|mixed|null|Response
     */
    public function update($id, $data = null)
    {
        foreach ($this->extra_field as $field){
            $this->builder()->fields()->push($field);
        }
        return parent::update($id,$data);
    }

    public function store()
    {
        foreach ($this->extra_field as $field){
            $this->builder()->fields()->push($field);
        }
        return parent::store(); // TODO: Change the autogenerated stub
    }

    /**
     * 定义的必填的Text字段
     * @param $column
     * @param $label
     * @param array $extraRules
     * @param array $extraMessages
     * @return mixed
     */
    public function iRequiredText($column, $label,array $extraRules = [],array $extraMessages = []) {
        $this->text($column,$label)->rules(function($form) use ($extraRules){
            return  array_merge([
                'required',
            ],$extraRules);
        },array_merge([
            'required'=> '请填写'.$label,
        ],$extraMessages));

        return $this;
    }

    /**
     * 自定义的密码、确认密码字段
     * @param $help
     * @return mixed
     */
    public function iPassword($help = '') {
         $this->password('password', trans('admin.password'))->rules('between_if_not_empty:6,20|confirmed',['required' => '请填写'.trans('admin.password'),
            'confirmed' => '两次密码填写不一致','between_if_not_empty'=>'密码应为6-20个字符'])->setWidth(3)->help($help);
        $this->password('password_confirmation', trans('admin.password_confirmation'))->rules('',[ 'required' => '请填写'.trans('admin.password_confirmation')])
            ->default('')->setWidth(3);
        $this->ignore(['password_confirmation']);
        return $this;
    }

    /**
     * 自定义的创建时间、修改时间字段
     */
    public function iTimeStamps() {
        if(!$this->timestamps) return;
        $this->datetime('created_at', trans('admin.created_at'))->readOnly()->placeholder('无');
        $this->datetime('updated_at', trans('admin.updated_at'))->readOnly()->placeholder('无');
    }

    /**
     * 不显示创建时间、修改时间字段
     */
    public function iDisableTimestamps() {
        $this->timestamps = false;
    }


}
