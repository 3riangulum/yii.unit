<?php

namespace Triangulum\Yii\Unit\Data\Elastic\Explorer;

use anticdroid\elasticsearch\Query;
use Triangulum\Yii\Unit\Data\Elastic\ElasticRepository;
use Triangulum\Yii\Unit\Data\Explorer\DataExplorerBase;

abstract class ElasticDataExplorer extends DataExplorerBase
{
    protected ?ElasticRepository $repository      = null;
    protected ?Query             $gridSearchQuery = null;

    protected function getQuery(): Query
    {
        return $this->gridSearchQuery;
    }

    protected function getQueryClone(): Query
    {
        return clone $this->gridSearchQuery;
    }

    protected function setQuery(Query $query): void
    {
        $this->gridSearchQuery = clone $query;
    }
}
