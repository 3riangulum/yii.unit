<?php

namespace Triangulum\Yii\Unit\Data\Db;

use Triangulum\Yii\Unit\Data\DataHandler;
use yii\db\ActiveRecord;

abstract class EntityBase extends ActiveRecord implements Entity
{
    use DataHandler;

    protected bool $dbSingleTransaction = true;

    public function transactions(): array
    {
        if ($this->dbSingleTransaction) {
            return [
                self::SCENARIO_DEFAULT => self::OP_ALL,
            ];
        }

        return [];
    }

    public function disableDbSingleTransaction(): void
    {
        $this->dbSingleTransaction = false;
    }

    public static function tbName(string $field = ''): string
    {
        $field = !empty($field) ? '.' . $field : '';

        return get_called_class()::getTableSchema()->fullName . $field;
    }
}
