<?php

namespace Triangulum\Yii\Unit\Data\Explorer;

use yii\data\Pagination;

class PaginationLimited extends Pagination
{
    public int $totalLimit = 10000;

    public function getPageCount(): int
    {
        $pageSize = $this->getPageSize();
        if ($pageSize < 1) {
            return $this->totalCount > 0 ? 1 : 0;
        }

        $totalCount = $this->totalCount < 0 ? 0 : (int)$this->totalCount;

        if ($totalCount > $this->totalLimit) {
            $totalCount = $this->totalLimit;
        }

        return (int)(($totalCount + $pageSize - 1) / $pageSize);
    }
}
