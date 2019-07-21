<?php

namespace LTBackup\Extension\Support;

use LTBackup\Extension\Facades\Admin;
use LTBackup\Extension\Tools\Grid\Grid;

use Closure;
/**
 * Class GridViewBuilder
 * @author wanghouting
 * @package LTBackup\Extension\Support
 */
class GridViewBuilder {
    /**
     * @var Grid;
     */
    protected $grid = null;

    /**
     * 创建表格
     * @param $model
     * @param Closure $callback
     * @return $this
     * @deprecated please use LTBackup\Extension\Facades\GridBuilder::buildGrid
     */
    public function buildGrid($model,Closure $callback ) {

        $this->grid = Admin::grid($model,$callback);
        return $this;
    }

    /**
     * 返回创建好的表格
     * @return Grid
     */
    public function get() {
        return $this->grid;
    }
}
