<?php

namespace Triangulum\Yii\Unit\Data\Db;

abstract class DbRepositoryAbstract implements DbRepository
{
    public function dataSave(Entity $entity, array $data): bool
    {
        if ($entity->load($data)) {
            return $this->save($entity);
        }

        return false;
    }
}
