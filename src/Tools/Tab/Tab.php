<?php

namespace LTBackup\Extension\Tools\Tab;


class Tab extends \Encore\Admin\Widgets\Tab {

    public function setActive(int $index) {
        $this->data['active'] = $index;
    }

}
