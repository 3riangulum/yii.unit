<?php

namespace Triangulum\Yii\Unit\Content\Email;

use Triangulum\Yii\Unit\Admittance\Admittance;
use Yii;

final class EmailShield
{
    protected float       $ratio            = 0.3;
    protected string      $permission       = 'allow-display-full-email';
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

    public function isAllowed(): bool
    {
        return $this->allowFullAddress;
    }

    public function recast(string $email = null, string $replace = '*'): string
    {
        return $this->isAllowed() ? $email : $this->obscure($email, $replace);
    }

    public function obscure(string $text = null, string $replace = '*'): string
    {
        if (empty($text)) {
            return '';
        }

        preg_match_all('/(\S+)(@(\S+))/u', $text, $match);
        if (empty($match[0]) || count($match) !== 4) {
            return $text;
        }

        [$emailList, $prefixList, $domainList] = $match;

        foreach ($emailList as $i => $email) {
            $cutLen = strlen($prefixList[$i]) * $this->ratio;
            $partialEmail = substr($prefixList[$i], 0, -ceil($cutLen)) . $replace . $domainList[$i];

            $text = str_replace($email, $partialEmail, $text);
        }

        return $text;
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
