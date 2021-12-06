<?php

namespace Triangulum\Yii\Unit\Data\Elastic;

use anticdroid\elasticsearch\Query;

interface ElasticRepository
{
    public function query(int $limit = null): Query;
}
