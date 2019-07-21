<?php


namespace LTBackup\Extension\Tools\Form;


class Field extends \Encore\Admin\Form\Field
{
    protected $iValue = null;


    public function iValue($value)
    {
        $this->iValue = $value;
        return $this;
    }

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return void
     */
    public function fill($data)
    {
        // Field value is already setted.
        if (!is_null($this->iValue)) {
            $this->value = $this->iValue;
            return;
        }
        parent::fill($data);

    }

}