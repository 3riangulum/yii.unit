<?php

namespace Triangulum\Yii\Unit\Data\Db\Explorer;

use Triangulum\Yii\Unit\Data\Db\DbRepository;
use Triangulum\Yii\Unit\Data\Db\Query\QueryBase;
use Triangulum\Yii\Unit\Data\Explorer\DataExplorerBase;

abstract class DbDataExplorer extends DataExplorerBase
{
    protected ?DbRepository $repository      = null;
    protected ?QueryBase    $gridSearchQuery = null;

    protected function getQuery(): QueryBase
    {
        return $this->gridSearchQuery;
    }

    protected function getQueryClone(): QueryBase
    {
        return clone $this->gridSearchQuery;
    }

    protected function setQuery(QueryBase $query): void
    {
        $this->gridSearchQuery = clone $query;
    }
}
