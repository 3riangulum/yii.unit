<?php

namespace Triangulum\Yii\Unit\Front\UI;

use Triangulum\Yii\Unit\Admittance\RouteBase;
use Triangulum\Yii\Unit\Front\FrontConfig;
use Triangulum\Yii\Unit\Front\Items\FrontSimple;
use yii\helpers\Html;

class ButtonActiveBase
{
    private ?RouteBase   $router       = null;
    private string       $action       = '';
    private string       $template     = '';
    private string       $buttonTitle  = '';
    private string       $alertTitle   = '';
    private string       $alertSuccess = '';
    private string       $alertError   = '';
    private ?FrontSimple $ui           = null;

    public function __construct(
        RouteBase $router,
        string    $action,
        string    $template,
        string    $buttonTitle,
        string    $alertTitle,
        string    $alertSuccess,
        string    $alertError
    ) {
        $this->router = $router;
        $this->action = $action;
        $this->template = $template;
        $this->buttonTitle = $buttonTitle;
        $this->alertTitle = $alertTitle;
        $this->alertSuccess = $alertSuccess;
        $this->alertError = $alertError;
    }

    public function template(): string
    {
        return '@backend-views/' . $this->router->unitAlias() . '/' . $this->template;
    }

    public function button(int $uid, bool $success = null): void
    {
        if (!$this->router->isAllowed($this->action)) {
            return;
        }

        $this->ui()->pjaxBegin(false);
        echo Html::a(
            $this->buttonTitle,
            [
                $this->router->route($this->action),
                'uid' => $uid ? $uid : '',
            ],
            [
                'class' => 'btn btn-xs btn-default',
                'data'  => [
                    'confirm'        => 'Are you sure you want to <span class="label label-danger">send letter</span>?',
                    'pjax-container' => '#' . $this->ui()->pjaxId(),
                    'pjax'           => true,
                    'method'         => 'post',
                ],
            ]
        );
        $this->ui()->alert($success ? $this->alertSuccess : $this->alertError, $success);
        $this->ui()->pjaxEnd();
    }

    private function ui(): FrontSimple
    {
        if (null === $this->ui) {
            $alias = 'alias';
            $config = FrontConfig::build(
                ['router' => $this->router]
            )->buildPopup(
                $alias,
                $this->action,
                false,
                false
            )->export()[$alias];

            $config['pjaxConfig'] = [
                'linkSelector'  => true,
                'clientOptions' => ['method' => 'POST'],
                'options'       => ['class' => 'col-centered'],
            ];

            $this->ui = FrontSimple::builder($config)->setTitle($this->alertTitle);
        }

        return $this->ui;
    }
}
