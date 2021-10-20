<?php

namespace Triangulum\Yii\Unit\Data\Db;

use yii\db\ActiveQueryInterface;
use yii\db\Transaction;

interface DbRepository
{
    public function query(): ActiveQueryInterface;

    public function create(): Entity;

    public function single(int $pk, bool $throw = true): ?Entity;

    public function beginTransaction(): Transaction;
}
