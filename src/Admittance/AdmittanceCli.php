<?php

namespace Triangulum\Yii\Unit\Admittance;

class AdmittanceCli implements Admittance
{
    public function getUid(): int
    {
        return 0;
    }

    public function hasRoleRoot(): bool
    {
        return false;
    }

    public function can(string $rule): bool
    {
        return false;
    }
}
