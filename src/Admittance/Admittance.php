<?php

namespace Triangulum\Yii\Unit\Admittance;

interface Admittance
{
    public function getUid(): int;

    public function hasRoleRoot(): bool;

    public function can(string $rule): bool;
}
