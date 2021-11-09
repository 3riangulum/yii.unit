<?php

namespace Triangulum\Yii\Unit\Front;

interface Front
{
    public static function build(): self;

    public function template(string $template): string;

    public function getTitle(): string;
}
