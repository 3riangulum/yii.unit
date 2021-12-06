<?php

namespace Triangulum\Yii\Unit\Front;

use Triangulum\Yii\Unit\Admittance\RouteBase;
use Yii;
use yii\helpers\BaseInflector;
use yii\helpers\Inflector;

final class FrontConfig
{
    public ?RouteBase $router = null;

    public string $deleteAction = '';
    public string $delete       = '';
    public string $gridClass    = '';

    private string  $gridId       = 'grid';
    private string  $reloadGridId = '';
    private string  $dataMapper   = "['id']";
    private ?string $gridTag      = null;
    private array   $actionConfig = [];

    public function __construct(RouteBase $router)
    {
        $this->router = $router;
    }

    public static function build(array $config = []): self
    {
        $config['class'] = self::class;

        return Yii::createObject($config);
    }

    public function mix(self $frontConfig): self
    {
        foreach ($frontConfig->export() as $alias => $config) {
            $this->actionConfig[$alias] = $config;
        }

        return $this;
    }

    public function buildGrid(string $alias, string $action, string $title = '', array $uri = []): self
    {
        $this->actionConfig[$alias] = [
            'route'       => $this->router->route($action, $uri),
            'allowAction' => $this->router->isAllowed($action),
            'gridId'      => $this->gridTag(),
            'class'       => $this->gridClass,
            'title'       => $title,
        ];

        return $this;
    }

    public function buildPopup(string $alias, string $action, bool $gridRefresh = true, bool $delete = true, string $dataMapper = "['id']"): self
    {
        $this->actionConfig[$alias] = [
            'tag'          => $this->tagByAction($action),
            'route'        => $this->router->route($action),
            'allowAction'  => $this->router->isAllowed($action),
            'reloadGridId' => $gridRefresh ? $this->gridTag() : '',
            'actionDelete' => $delete ? $this->deleteAction : '',
            'dataMapper'   => $dataMapper,
        ];

        return $this;
    }

    public function buildDelete(): self
    {
        if (!empty($this->delete)) {
            $this->buildPopup($this->delete, $this->delete);
        }

        return $this;
    }

    public function tagFilter(string $alias): string
    {
        return BaseInflector::underscore(
            Inflector::variablize($alias),
            '_'
        );
    }

    public function tagByAction(string $action): string
    {
        return $this->tagFilter(
            $this->router->rule($action)
        );
    }

    protected function gridTag(): string
    {
        if (null === $this->gridTag) {
            $this->gridTag = $this->tagFilter(
                $this->router->unitAlias() . '_' . $this->gridId
            );
        }

        return $this->gridTag;
    }

    protected function gridId(string $gridId = ''): string
    {
        return $this->tagFilter($this->router->unitAlias()) . '_' . ($gridId ?? '');
    }

    public function export(string $alias = ''): array
    {
        return empty($alias) ?
            $this->actionConfig :
            ($this->actionConfig[$alias] ?? []);
    }
}
