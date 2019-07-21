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
        return view('admin::content', $items)->render();
    }

    /**
     * @param $header
     * @param $description
     * @param $body
     * @param string $breadcrumb
     * @return $this
     */
    public function init($header, $description,$body = '', $breadcrumb = '') {
        !empty($header) && $this->header = $header;
        !empty($description) && $this->description = $description;
        !empty($body) && $this->body($body);
        !empty($breadcrumb) && $this->breadcrumb = $breadcrumb;
        return $this;
    }
}
