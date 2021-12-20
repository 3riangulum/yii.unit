<?php

namespace Triangulum\Yii\Unit\Admittance\IpAddress;

interface IpAddressInspectorRepository
{
    public function listAllowed(): array;
}
