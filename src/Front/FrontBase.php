<?php

namespace Triangulum\Yii\Unit\Front;

use Triangulum\Yii\Unit\Admittance\RouteBase;
use Triangulum\Yii\Unit\Data\DataExplorer;
use Triangulum\Yii\Unit\Front\Items\FrontSimple;
use Yii;

abstract class FrontBase implements Front
{
    public const ALIAS_GRID       = 'grid';
    public const ALIAS_EDITOR     = 'editor';
    public const ALIAS_CREATOR    = 'creator';
    public const ALIAS_DUPLICATOR = 'duplicator';
    public const ALIAS_ERASER     = 'eraser';
    public const ALIAS_VIEWER     = 'viewer';

    protected ?string    $gridSortableAction = null;
    protected bool       $gridFilterEnable   = true;
    protected ?string    $gridClass          = null;
    protected ?RouteBase $router             = null;
    protected array      $actionConfig       = [];
    protected string     $viewPath           = '';

    abstract protected function loadDataExplorer(): DataExplorer;

    public function __construct(RouteBase $router)
    {
        $this->router = $router;
        $this->viewPath = '@backend-views/' . $this->router->unitAlias() . '/';
    }

    public static function build(): self
    {
        return Yii::createObject(static::class);
    }

    public function template(string $template): string
    {
        return $this->viewPath . trim($template, '/');
    }

    protected function actionConfig(): array
    {
        return $this->actionConfig;
    }

    protected function actionIsAllowed(string $alias): bool
    {
        return $this->actionConfig()[$alias]['allowAction'] ?? false;
    }

    protected function uiUnitLoad(string $alias): FrontSimple
    {
        return FrontSimple::builder($this->actionConfig()[$alias]);
    }
//
//    protected function autocompleteGrid(Model $model, string $attribute): AutoCompleteSelectGrid
//    {
//        return AutoCompleteSelectGrid::builder([
//            'model'     => $model,
//            'attribute' => $attribute,
//        ]);
//    }
}
