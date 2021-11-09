<?php

namespace Triangulum\Yii\Unit\Data\Validator;

trait ValidatorCommon
{
    public function isArrayValidation($attribute, $params): void
    {
        if (!is_array($this->$attribute)) {
            $this->addError($attribute, 'Is not array');

            return;
        }

        $list = $this->$attribute;
        sort($list);
        $this->$attribute = $list;
    }

    public function isInZeroOneValidation($attribute, $params): void
    {
        if (!in_array($this->$attribute, [0, 1], true)) {
            $this->addError($attribute, 'Allowed "0" or "1" values');
        }
    }

    public function filterIntNull($value = null): ?int
    {
        $value = (int)$value;

        return empty($value) ? null : $value;
    }

    public function filterFloatNull($value = null): ?float
    {
        $value = (float)$value;

        return empty($value) ? null : $value;
    }

    public function filterStringTrimNull($value = null): ?string
    {
        $value = trim((string)$value);

        return empty($value) ? null : $value;
    }
}
