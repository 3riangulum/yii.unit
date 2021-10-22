<?php

namespace Triangulum\Yii\Unit\Data;

trait DataHandler
{
    public function dataValidate(array $postData, bool $isValid = null): bool
    {
        if ($isValid !== null && !$isValid) {
            return false;
        }

        return $this->load($postData) && $this->validate();
    }

    public function exportAttributes(array $exclude = []): array
    {
        $data = $this->getAttributes();

        foreach ($exclude as $attribute) {
            unset($data[$attribute]);
        }

        return $data;
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
}
