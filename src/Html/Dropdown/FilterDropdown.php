<?php

namespace Triangulum\Yii\Unit\Html\Dropdown;

use Yii;

final class FilterDropdown
{
    public array  $itemMap             = [];
    public string $labelContainerClass = 'text-center';

    public static function build(array $itemMap): self
    {
        return Yii::createObject([
            'class'   => self::class,
            'itemMap' => $itemMap,
        ]);
    }

    public function items(): array
    {
        $ret = [];
        foreach ($this->itemMap as $status => $label) {
            $ret[] = [
                'id'   => $status,
                'text' => $this->label($status),
            ];
        }

        return $ret;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function label($value): string
    {
        return '<div class="' . $this->labelContainerClass . '">' . $this->labelText($value) . '</div>';
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function labelText($value): ?string
    {
        return $this->itemMap[$value] ?? null;
    }
}
