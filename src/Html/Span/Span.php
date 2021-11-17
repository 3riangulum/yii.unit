<?php

namespace Triangulum\Yii\Unit\Html\Span;

final class Span
{
    public static function spanStrong(string $content, string $class = ''): string
    {
        return self::span($content, "text-strong $class");
    }

    public static function span(string $content, string $class = ''): string
    {
        return <<< HTML
<span class="$class">$content</span>
HTML;
    }
}
