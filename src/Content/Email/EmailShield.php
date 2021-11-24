<?php

namespace Triangulum\Yii\Unit\Content\Email;

use Triangulum\Yii\Unit\Admittance\Admittance;
use Yii;

final class EmailShield
{
    protected float       $ratio            = 0.3;
    protected string      $permission       = 'email-address-display';
    protected ?Admittance $admittance       = null;
    private bool          $allowFullAddress = false;

    public function __construct(Admittance $admittance)
    {
        $this->admittance = $admittance;
        $this->defineAccess();
    }

    public static function build(): self
    {
        return Yii::createObject(self::class);
    }

    public function get(string $email = null, string $replace = '*'): string
    {
        return $this->allowFullAddress ? $email : $this->partial($email, $replace);
    }

    public function partial(string $email = null, string $replace = '*'): string
    {
        if (empty($email)) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $cutLen = strlen($parts[0]) * $this->ratio;

        return substr($parts[0], 0, -ceil($cutLen)) . $replace . '@' . $parts[1];
    }

    public function setRatio(float $ratio): void
    {
        $this->ratio = $ratio;
    }

    public function setPermission(string $permission): void
    {
        $this->permission = $permission;
        $this->defineAccess();
    }

    private function defineAccess(): void
    {
        $this->allowFullAddress = $this->admittance->can($this->permission);
    }
}
