<?php

namespace Triangulum\Yii\Unit\Html\Form;

use Triangulum\Yii\Unit\Data\DataBasic;
use Triangulum\Yii\Unit\Front\Items\FrontSimple;
use Triangulum\Yii\Unit\Html\Button\Button;
use Yii;
use yii\base\BaseObject;
use yii\widgets\ActiveForm;

class DataForm extends BaseObject
{
    public ?FrontSimple $ui     = null;
    public ?DataBasic   $entity = null;
    public array $formConfig = [];

    protected ?ActiveForm $form = null;

    public static function build(array $config): self
    {
        if (!isset($config['class'])) {
            $config['class'] = static::class;
        }

        return Yii::createObject($config);
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->ui->setHasError($this->entity->hasErrors());
    }

    public function begin(): void
    {
        $this->ui->pjaxBegin();
        $this->ui->panelBegin();
        $this->form = $this->ui->formPostBegin($this->formConfig);
    }

    public function end(): void
    {
        ActiveForm::end();
        $this->ui->panelEnd();
        $this->ui->pjaxEnd();
    }

    public function submitTop(): string
    {
        return Button::submitTop();
    }

    public function submitBottom(): string
    {
        return Button::submitBottom();
    }
}
