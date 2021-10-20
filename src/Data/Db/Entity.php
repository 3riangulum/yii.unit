<?php

namespace Triangulum\Yii\Unit\Data\Db;

interface Entity
{
    public function exportAttributes(array $exclude = []): array;

    public function disableDbSingleTransaction(): void;

    public static function tbName(string $field = ''): string;

    public function pkGet(): int;
}
