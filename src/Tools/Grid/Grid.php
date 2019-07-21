<?php


namespace LTBackup\Extension\Tools\Grid;

use Closure;
use Encore\Admin\Grid\Column;
use Illuminate\Database\Eloquent\Model as Eloquent;
use LTBackup\Extension\Tools\Grid\Displayers\SwitchDisplay;

/**
 * Class Grid
 * @author wanghouting
 * @package LTBackup\Extension\Tools\Grid
 */
class Grid extends \Encore\Admin\Grid {


    public function __construct(Eloquent $model, Closure $builder = null) {
        parent::__construct($model, $builder);
        Column::extend('iSwitch', SwitchDisplay::class);
    }


    /**
     * 自定义的按钮显示
     * @param bool $disableView
     * @param bool $disableEdit
     * @param bool $disableDelete
     * @return Grid|void
     */
    public function iDisableActions($disableView = true, $disableEdit = true, $disableDelete = true) {
        if(!$disableView && !$disableEdit && !$disableDelete) return;
        if($disableView &&  $disableEdit && $disableDelete) return $this->disableActions();
        $this->actions(function (Actions $actions) use ($disableView,$disableEdit,$disableDelete){
            $disableView && $actions->disableView();
            $disableEdit && $actions->disableEdit();
            $disableDelete && $actions->disableDelete();
        });

    }

    /**
     * Add `actions` column for grid.
     *
     * @return void
     */
    protected function appendActionsColumn()
    {
//        if (!$this->option('useActions')) {
//            return;
//        }
        $this->addColumn('__actions__', trans('admin.action'))
            ->displayUsing(Actions::class, [$this->actionsCallback]);
    }

    /**
     * 自定义的id字段
     */
    public function iId(){
        $this->id('ID')->sortable()->style('width:80px;');
    }


    /**
     * 自定义的switch字段
     * @param $column
     * @param $label
     * @param array $state
     */
    public function iSwitch($column,$label,array  $state = []) {
        $state = empty($state) ?  get_switch_state() : $state;
        $this->column($column,$label)->sortable()->iSwitch($state);
    }


    /**
     * 自定义的创建时间、修改时间字段
     */
    public function iTimeStamps() {
        $this->column('created_at', trans('admin.created_at'))->sortable();
        $this->column('updated_at', trans('admin.updated_at'))->sortable();
    }



}
