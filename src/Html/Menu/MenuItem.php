<?php

namespace Triangulum\Yii\Unit\Html\Menu;

use Triangulum\Yii\Unit\Admittance\RouteBase;
use Yii;

class MenuItem
{
    public ?RouteBase $router = null;
    public string     $title  = '';
    public string     $action = '';

    public static function build(RouteBase $router, string $title, string $action): self
    {
        return Yii::createObject([
            'class'  => self::class,
            'router' => $router,
            'title'  => $title,
            'action' => $action,
        ]);
    }

    public function export(): array
    {
        return [
            'label'   => $this->title,
            'url'     => $this->router->route($this->action),
            'alias'   => [$this->controllerAlias()],
            'visible' => $this->isAllowed(),
            'icon'    => ' ',
        ];
    }

    public function isAllowed(): bool
    {
        return $this->router->isAllowed($this->action);
    }

    public function controllerAlias(): string
    {
        return $this->router->unitAlias();
    }
}
