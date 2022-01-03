<?php

namespace Triangulum\Yii\Unit\Admittance;

use Exception;
use Throwable;
use Triangulum\Yii\Unit\Cache\RedisTaggedCache;
use Yii;
use yii\di\Instance;

class AdmittanceBase implements Admittance
{
    public string $cacheAlias    = 'cache';
    public int    $cacheDuration = 3600;
    public string $roleRoot      = 'role-root';

    public function getUid(): int
    {
        try {
            $uid = (int)Yii::$app->user->getIdentity()->getId();
            if (empty($uid)) {
                throw new Exception('Unauthorized access');
            }

            return $uid;
        } catch (Throwable $t) {
            throw new Exception('Unauthorized access');
        }
    }

    public function hasRoleRoot(): bool
    {
        return Yii::$app->user->can($this->roleRoot);
    }

    public function can(string $rule): bool
    {
        return isset($this->listUserPermissions()[$rule]);
    }

    private function listUserPermissions(): array
    {
        return Instance::ensure($this->cacheAlias, RedisTaggedCache::class)->getOrSet(
            [__METHOD__, 'web', $this->getUid()],
            function () {
                return array_fill_keys(
                    array_keys(
                        Yii::$app->authManager->getPermissionsByUser($this->getUid())
                    ),
                    0
                );
            },
            $this->cacheDuration
        );
    }
}
