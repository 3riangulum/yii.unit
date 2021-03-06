<?php

namespace Triangulum\Yii\Unit\Data\Explorer;

use Triangulum\Yii\Unit\Data\DataBasic;
use Triangulum\Yii\Unit\Data\DataExplorer;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

abstract class DataExplorerBase extends DataBasic implements DataExplorer
{
    protected string      $gridSearchFormAlias = '';
    protected array       $gridSearchParams    = [];
    protected ?array      $sort                = null;
    protected ?Pagination $pagination          = null;
    public ?string        $route               = null;

    public static function build(array $config = []): self
    {
        if (empty($config['class'])) {
            $config['class'] = static::class;
        }

        return Yii::createObject($config);
    }

    public function loadParams(array $searchParams): bool
    {
        $this->initParams($searchParams, $this->formName());
        if (!$this->load($this->paramsGetAll())) {
            return false;
        }

        return $this->validate();
    }

    public function loadEmptyProvider(string $route = null): ArrayDataProvider
    {
        return new ArrayDataProvider([
            'allModels'  => [],
            'pagination' => [
                'pageSize' => 0,
                'route'    => $route,
            ],
        ]);
    }

    protected function loadDataProvider(): ActiveDataProvider
    {
        $cfg = ['query' => $this->getQuery()];
        if (null !== $this->sort) {
            $cfg['sort'] = $this->sort;
            if (!empty($this->route)) {
                $cfg['sort']['route'] = $this->route;
            }
        }

        if (null !== $this->pagination) {
            if (!empty($this->route)) {
                $this->pagination->route = $this->route;
            }
            $cfg['pagination'] = $this->pagination;
        }

        return new ActiveDataProvider($cfg);
    }

    protected function initParams(array $params, string $formAlias): void
    {
        $this->setGridSearchFormAlias($formAlias);
        $this->setParams($params);
    }

    protected function paramExist(string $alias): bool
    {
        return isset($this->gridSearchParams[$this->gridSearchFormAlias][$alias]);
    }

    protected function paramNotEmpty(string $alias): bool
    {
        $val = $this->paramGetValue($alias);
        if (is_string($val)) {
            $val = trim($val);
        }

        return !empty($val);
    }

    protected function paramExistNotEmpty(string $alias): bool
    {
        return $this->paramExist($alias) && $this->paramNotEmpty($alias);
    }

    protected function paramListExistNotEmpty(array $aliasList): bool
    {
        foreach ($aliasList as $alias) {
            if (!$this->paramExist($alias) || !$this->paramNotEmpty($alias)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return mixed
     */
    protected function paramGetValue(string $alias, $default = null)
    {
        return $this->gridSearchParams[$this->gridSearchFormAlias][$alias] ?? $default;
    }

    /**
     * @param string $alias
     * @param mixed  $value
     */
    protected function paramSetIfNotExist(string $alias, $value): void
    {
        if (!$this->paramExist($alias)) {
            $this->paramSetValue($alias, $value);
        }
    }

    /**
     * @param string $alias
     * @param mixed  $value
     */
    protected function paramSetValue(string $alias, $value): void
    {
        $this->gridSearchParams[$this->gridSearchFormAlias][$alias] = $value;
    }

    protected function paramUnset(string $alias): void
    {
        unset($this->gridSearchParams[$this->gridSearchFormAlias][$alias]);
    }

    public function paramsGetAll(): array
    {
        return [
            $this->gridSearchFormAlias => $this->gridSearchParams[$this->gridSearchFormAlias] ?? [],
        ];
    }

    protected function setParams(array $params): void
    {
        $this->gridSearchParams = array_merge($this->gridSearchParams, $params);
    }

    protected function setGridSearchFormAlias(string $alias): void
    {
        $this->gridSearchFormAlias = $alias;
    }

    public function paramExportNotEmpty(): array
    {
        $ret = [];
        foreach ($this->paramsGetAll()[$this->gridSearchFormAlias] ?? [] as $name => $value) {
            if ($this->paramExistNotEmpty($name)) {
                $ret[$name] = $value;
            }
        }

        return $ret;
    }
}
