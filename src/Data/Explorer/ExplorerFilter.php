<?php

namespace Triangulum\Yii\Unit\Data\Explorer;

use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use Triangulum\Yii\Unit\Html\Checkbox\CheckBox;
use Triangulum\Yii\Unit\Html\Checkbox\CheckboxWidget;
use Triangulum\Yii\Unit\Html\Dropdown\Dropdown;
use Triangulum\Yii\Unit\Html\Dropdown\FilterDropdown;
use yii\helpers\Html;

trait ExplorerFilter
{
    private string $elementClass = 'form-control';

    public function dropDown(FilterDropdown $filter, string $attribute): string
    {
        return Dropdown::element(
            $this,
            $attribute,
            $filter->items(),
            $filter->label($this->{$attribute})
        );
    }

    public function htmlDropDownCustom(string $field, array $items, string $selected = null): string
    {
        return Dropdown::element($this, $field, $items, $selected);
    }

    public function htmlDropDownList(string $name, $items = [], $options = [], $default = null): string
    {
        $options['class'] = ($options['class'] ?? '') . ' ' . $this->elementClass;

        return Html::dropDownList(
            self::getInputName($name),
            $this->getFilterValidatedValue($name, $default),
            $items,
            $options
        );
    }

    public function htmlTextInput(string $name, array $options = [], $default = ''): string
    {
        $options['class'] = ($options['class'] ?? '') . ' ' . $this->elementClass;

        return Html::textInput(
            $this->getFilterInputName($name),
            $this->getFilterValidatedValue($name, $default),
            $options
        );
    }

    public function datePickerFilter(string $field, string $placeHolder = ''): string
    {
        return DatePicker::widget([
            'id'            => 'dp_' . $this->getFormAlias() . '_' . $field,
            'name'          => $this->getFilterInputName($field),
            'layout'        => '{input}{remove}',
            'value'         => substr(trim($this->getFilterValidatedValue($field)), 0, 10),
            'type'          => DatePicker::TYPE_COMPONENT_PREPEND,
            'pluginOptions' => [
                'format'         => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'autoclose'      => true,
            ],
            'options'       => [
                'placeholder'  => $placeHolder,
                'class'        => $this->elementClass,
                'autocomplete' => 'off',
            ],
        ]);
    }

    public function datePickerFilterPeriod(array $conf): string
    {
        $ret = '';
        foreach ($conf as $field => $placeholder) {
            $ret .= $this->datePickerFilter($field, $placeholder);
        }

        return $ret;
    }

    public function dateTimePickerFilterActive(string $field, string $placeHolder = '', bool $start = true): string
    {
        return DateTimePicker::widget([
            'id'            => 'dp_' . $this->getFormAlias() . '_' . $field,
            'name'          => $this->getFilterInputName($field),
            'layout'        => '{input}{remove}',
            'value'         => substr(trim($this->getFilterValidatedValue($field)), 0, 20),
            'type'          => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'pluginOptions' => [
                'format'         => 'yyyy-mm-dd hh:ii:' . ($start ? '00' : '59'),
                'minuteStep'     => 1,
                'todayHighlight' => true,
                'autoclose'      => true,
            ],
            'options'       => [
                'placeholder'  => $placeHolder,
                'class'        => $this->elementClass . ' date-time-picker',
                'autocomplete' => 'off',
            ],
        ]);
    }

    public function dateTimePickerFilterPeriod(array $conf): string
    {
        $ret = '';
        $cnt = 1;
        foreach ($conf as $field => $placeholder) {
            $ret .= $this->dateTimePickerFilterActive($field, $placeholder, $cnt === 1);
            $cnt++;
        }

        return $ret;
    }

    public function pairTextFilter(array $conf): string
    {
        $ret = '';
        foreach ($conf as $field => $placeholder) {
            $ret .= $this->htmlTextInput($field, ['placeholder' => $placeholder]);
        }

        return $ret;
    }

    public function htmlCheckBox(string $attribute, string $label): string
    {
        return CheckboxWidget::widget(
            CheckBox::loadOption(
                false,
                [
                    'model'     => $this,
                    'attribute' => $attribute,
                    'type'      => CheckboxWidget::TYPE_CHECKBOX,
                    'style'     => CheckboxWidget::STYLE_PRIMARY,
                    'options'   => [
                        'label' => $label,
                        'class' => 'checkbox-list-container',
                    ],
                ]
            )
        );
    }

    public function htmlRadioList(string $attribute, array $list, string $label): string
    {
        return CheckboxWidget::widget([
            'model'          => $this,
            'attribute'      => $attribute,
            'type'           => CheckboxWidget::TYPE_RADIO,
            'style'          => CheckboxWidget::STYLE_PRIMARY,
            'list'           => $list,
            'options'        => [
                'label' => $label,
                'class' => 'checkbox-list-container text-nowrap small text-center',
            ],
            'wrapperOptions' => [
                'class' => 'checkbox-inline checkbox-inline-list-slim ',
            ],
        ]);
    }
}
