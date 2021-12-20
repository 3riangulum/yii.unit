<?php

namespace Triangulum\Yii\Unit\Admittance\IpAddress;

use Triangulum\Yii\Unit\Admittance\Admittance;
use Yii;
use yii\base\BaseObject;
use yii\caching\CacheInterface;
use yii\di\Instance;

final class HttpIpAddressInspector extends BaseObject
{
    public array  $allowedIp        = [];
    public string $ignoreParam      = '';
    public string $ignoreParamValue = '';

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * @var Admittance
     */
    public $admittance;

    /**
     * @var IpAddressInspectorRepository
     */
    public $repository;

    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure($this->cache, CacheInterface::class);
        $this->admittance = Instance::ensure($this->admittance, Admittance::class);
        $this->repository = Instance::ensure($this->repository, IpAddressInspectorRepository::class);
        $this->allowedIp = $this->defineAllowedIp();
    }

    public function canIgnoreInspection(): bool
    {
        if (empty($this->ignoreParam) || empty($this->ignoreParamValue)) {
            return false;
        }

        return Yii::$app->getRequest()->get($this->ignoreParam) === $this->ignoreParamValue;
    }

    public function ipIsAllowed(): bool
    {
        if (!$this->canIgnoreInspection()) {
            return isset($this->allowedIp[$this->getRequestIp()]);
        }

        return true;
    }

    public function ipWasChanged(): bool
    {
        if (empty($cached = $this->getCachedIp())) {
            return true;
        }

        return $this->getRequestIp() !== $cached;
    }

    protected function getCachedIp(): string
    {
        return $this->cache->get($this->cacheKey());
    }

    public function getRequestIp(): string
    {
        if ($ip = filter_var(Yii::$app->getRequest()->getUserIP(), FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return '';
    }

    public function rememberIp(): void
    {
        if ($ip = $this->getRequestIp()) {
            $this->cache->set($this->cacheKey(), $ip);
        }
    }

    protected function cacheKey(): array
    {
        return [
            __METHOD__,
            $this->admittance->getUid(),
        ];
    }

    protected function defineAllowedIp(): array
    {
        return array_merge(
            $this->repository->listAllowed(),
            $this->allowedIp
        );
    }
}
