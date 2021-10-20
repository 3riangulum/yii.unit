<?php

namespace Triangulum\Yii\Unit\Data;

use yii\data\DataProviderInterface;

interface DataExplorer
{
    public function search(array $params): DataProviderInterface;

    public function loadEmptyProvider(string $route = null): DataProviderInterface;

    public function paramExportNotEmpty(): array;

    public function loadParams(array $searchParams): bool;
}
