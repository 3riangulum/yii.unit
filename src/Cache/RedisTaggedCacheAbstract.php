<?php

namespace Triangulum\Yii\Unit\Cache;

use Closure;
use Yii;
use yii\caching\TagDependency;

abstract class RedisTaggedCacheAbstract implements RedisTaggedCache
{
    protected string $cacheAlias = 'cache';

    public static function build(): self
    {
        return Yii::createObject(static::class);
    }

    protected function getDependency(): TagDependency
    {
        return new  TagDependency(['tags' => $this->getTag()]);
    }

    public function invalidate(): void
    {
        TagDependency::invalidate(
            Yii::$app->get($this->cacheAlias),
            $this->getTag()
        );
    }

    public function getOrSet(array $key, Closure $callable, $duration = null)
    {
        return Yii::$app
            ->get($this->cacheAlias)
            ->getOrSet(
                $key,
                $callable,
                $duration,
                $this->getDependency()
            );
    }

    public function get(array $key)
    {
        return Yii::$app
            ->get($this->cacheAlias)
            ->get($key);
    }

    public function set($key, $value, $duration = null)
    {
        return Yii::$app
            ->get($this->cacheAlias)
            ->set(
                $key,
                $value,
                $duration,
                $this->getDependency()
            );
    }
}
