<?php

namespace Triangulum\Yii\Unit\Data;

use ReflectionClass;
use yii\base\Model;
use yii\helpers\Html;

class DataBasic extends Model
{
    use DataHandler;

    public static function extractPost(array $postData, string $attribute = null)
    {
        $data = $postData[static::getInputName()] ?? [];

        if (empty($attribute)) {
            return $data;
        }

        return $data[$attribute] ?? null;
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
