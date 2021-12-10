<?php

namespace Triangulum\Yii\Unit\Html\AutoComplete;

use kartik\select2\Select2;

/**
 * @method static self build(array $params = [])
 */
class AutoCompleteSelectGrid extends AutoCompleteSelectAbstract
{
    public function widget(): string
    {
        $cfg = $this->getWidgetConfig();
        $cfg['model'] = $this->model;
        $cfg['attribute'] = $this->attribute;

        return Select2::widget($cfg);
    }
}
