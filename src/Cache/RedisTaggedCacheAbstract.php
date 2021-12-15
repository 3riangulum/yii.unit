<?php

namespace Triangulum\Yii\Unit\Cache;

use Closure;
use Yii;
use yii\caching\CacheInterface;
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
            $this->cache(),
            $this->getTag()
        );
    }

    public function getOrSet(array $key, Closure $callable, $duration = null)
    {
        return $this->cache()->getOrSet(
            $key,
            $callable,
            $duration,
            $this->getDependency()
        );
    }

    public function get(array $key)
    {
        return $this->cache()->get($key);
    }

    public function set($key, $value, $duration = null)
    {
        return $this->cache()->set(
            $key,
            $value,
            $duration,
            $this->getDependency()
        );
    }

    public function exists(array $key): bool
    {
        return $this->cache()->exists($key);
    }

    public function delete(array $key): void
    {
        $this->cache()->delete($key);
    }

    protected function cache(): CacheInterface
    {
        return Yii::$app->get($this->cacheAlias);
    }
}
