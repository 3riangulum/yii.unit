<?php

namespace Triangulum\Yii\Unit\Data\Db;

use Yii;

abstract class DbRepositoryAbstract implements DbRepository
{
    public static function build(): self
    {
        return Yii::createObject(static::class);
    }

    public function dataSave(Entity $entity, array $data): bool
    {
        if ($entity->load($data)) {
            return $this->save($entity);
        }

        return false;
    }
}
