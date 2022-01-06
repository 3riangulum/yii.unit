<?php

namespace Triangulum\Yii\Unit\Html\Menu;

use Triangulum\Yii\Unit\Admittance\Admittance;

/**
 * @method static self build
 */
class MenuItemRootAccess extends MenuItem
{
    private ?Admittance $admittance = null;

    public function __construct(Admittance $admittance)
    {
        $this->admittance = $admittance;
    }

    public function isAllowed(): bool
    {
        return $this->admittance->hasRoleRoot();
    }
}
