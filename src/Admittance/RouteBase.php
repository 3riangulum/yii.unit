<?php

namespace Triangulum\Yii\Unit\Admittance;

use Yii;

class RouteBase
{
    public const ACTION_INDEX     = 'index';
    public const ACTION_EDIT      = 'update';
    public const ACTION_CREATE    = 'create';
    public const ACTION_DELETE    = 'delete';
    public const ACTION_DUPLICATE = 'duplicate';
    public const ACTION_VIEW      = 'view';

    protected ?Admittance $admittance = null;
    protected string      $unitAlias  = '';
    protected array       $actions    = [];

    public function __construct(Admittance $admittance)
    {
        $this->admittance = $admittance;
    }

    public static function build(): self
    {
        return Yii::createObject(static::class);
    }

    public function route(string $action, array $param = []): string
    {
        if (empty($rule = $this->rule($action))) {
            return '';
        }

        return Yii::$app
            ->getUrlManager()
            ->createUrl(
                array_merge([$rule], $param)
            );
    }

    public function rule(string $action): string
    {

        return $this->unitAlias() . '/' . $this->action($action);
    }

    protected function action(string $action): string
    {
        if ('' === $action) {
            return '';
        }

        return in_array($action, $this->actions, true) ? $action : '';
    }

    public function isAllowed(string $action): bool
    {
        $rule = $this->rule($action);
        if ('' === $rule) {
            return false;
        }

        return $this->admittance->can($rule);
    }

    public function unitAlias(): string
    {
        return $this->unitAlias;
    }
}
