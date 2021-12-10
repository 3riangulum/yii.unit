<?php

namespace Triangulum\Yii\Unit\Html\AutoComplete;

use kartik\select2\Select2;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

/**
 * @method static self build(array $params = [])
 */
class AutoCompleteSelectForm extends AutoCompleteSelectAbstract
{
    public ?ActiveForm $form = null;

    public function widget(): ActiveField
    {
        return $this->form
            ->field($this->model, $this->attribute)
            ->widget(
                Select2::class,
                $this->getWidgetConfig()
            );
    }
}
