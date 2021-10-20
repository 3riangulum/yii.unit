<?php

namespace Triangulum\Yii\Unit\Data;

use ReflectionClass;
use yii\base\Model;
use yii\helpers\Html;

class DataBasic extends Model
{
    public function dataValidate(array $postData, bool $isValid = null): bool
    {
        if ($isValid !== null && !$isValid) {
            return false;
        }

        return $this->load($postData) && $this->validate();
    }

    public function errorsToString(string $separator = ' '): string
    {
        $propertyList = $this->attributeLabels();
        if (!$this->errors) {
            return '';
        }

        $ret = [];
        foreach ($this->errors as $alias => $errorList) {
            $name = !empty($propertyList[$alias]) ? $propertyList[$alias] : '';
            foreach ($errorList as $error) {
                $ret[] = $name . $error . $separator;
            }
        }

        return implode('', $ret);
    }

    public static function extractPost(array $postData, string $attribute = null)
    {
        $data = $postData[static::getInputName()] ?? [];

        if (empty($attribute)) {
            return $data;
        }

        return $data[$attribute] ?? null;
    }

    protected function exportNotEmpty(): array
    {
        $ret = [];
        $data = $this->toArray();
        foreach ($data as $field => $value) {
            if (empty($value)) {
                continue;
            }

            $ret[$field] = $value;
        }

        return $ret;
    }

    protected function getInputId(string $attribute): string
    {
        return Html::getInputId($this, $attribute);
    }

    public static function getInputName(string $attribute = null): string
    {
        $reflector = new ReflectionClass(get_called_class());
        $suffix = $attribute ? '[' . $attribute . ']' : '';

        return $reflector->getShortName() . $suffix;
    }
}
