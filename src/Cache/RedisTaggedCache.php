<?php

namespace Triangulum\Yii\Unit\Cache;

use Closure;

interface RedisTaggedCache
{
    public static function build(): self;

    public function getTag(): string;

    public function invalidate(): void;

    public function getOrSet(array $key, Closure $callable, $duration = null);

    public function get(array $key);

    public function set(array $key, $value, $duration = null);
}
