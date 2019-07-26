<?php

namespace LTBackup\Extension\Tools\Layout;

use Encore\Admin\Layout\Content as BaseContent;

/**
 * Class Content
 * @author  wanghouting
 * @package LTBackup\Extension\Tools\Layout
 */
class Content extends BaseContent
{
    public $view;
    public $header;
    /**
     * @return string
     * @throws \Throwable
     */
    public function render()
    {
        $items = [
            'header'      => $this->header,
            'description' => $this->description,
            'breadcrumb'  => $this->breadcrumb,
            'content'     => $this->build(),
        ];
        $view = $this->view ?? 'admin::content';
        return view($view, $items)->render();
    }

    public function setView($view){
        $this->view = $view;
    }


    /**
     * @param $header
     * @param $description
     * @param $body
     * @param string $breadcrumb
     * @return $this
     */
    public function init($header, $description,$body = '', $breadcrumb =  null) {
        !empty($header) && $this->header = $header;
        !empty($description) && $this->description = $description;
        !empty($body) && $this->body($body);
        $breadcrumb && $this->breadcrumb = $breadcrumb;
        return $this;
    }
}
