<?php

namespace Triangulum\Yii\Unit\Data\Db\Migration;

use yii\db\Migration;
use yii\rbac\Item;

class RbacMigration extends Migration
{
    protected array $roleList = ['root'];

    protected string $authItemTable      = 'auth_item';
    protected string $authItemChildTable = 'auth_item_child';
    protected array  $authItemColumns    = [
        'name',
        'type',
        'description',
        'created_at',
        'updated_at',
    ];

    protected array $authItemChildTableColumns = [
        'parent',
        'child',
    ];

    protected array $permissionCreateMap = [];
    protected array $roleCreateMap       = [];

    public function safeDown()
    {
        $this->delete($this->authItemTable, [
            'IN',
            'name',
            array_keys($this->permissionCreateMap),
        ]);

        if (!empty($this->roleCreateMap)) {
            $this->delete($this->authItemTable, [
                'IN',
                'name',
                array_keys($this->roleCreateMap),
            ]);
        }
    }

    public function safeUp()
    {
        $this->createPermissions();
        $this->createRoles();
        $this->bindPermissionsToRoles();
    }

    protected function createPermissions(): void
    {
        $this->batchInsert(
            $this->authItemTable,
            $this->authItemColumns,
            $this->prepareItemData($this->permissionCreateMap, Item::TYPE_PERMISSION)
        );
    }

    protected function prepareItemData($dataMap, int $type): array
    {
        $t = time();
        $data = [];
        foreach ($dataMap as $route => $description) {
            $data[] = [$route, $type, $description, $t, $t];
        }

        return $data;
    }

    protected function createRoles(): void
    {
        if (empty($this->roleCreateMap)) {
            return;
        }

        $this->batchInsert(
            $this->authItemTable,
            $this->authItemColumns,
            $this->prepareItemData($this->roleCreateMap, Item::TYPE_ROLE)
        );

        foreach (array_keys($this->roleCreateMap) as $role) {
            $this->roleList[] = $role;
        }
    }

    protected function bindPermissionsToRoles(): void
    {
        $this->batchInsert(
            $this->authItemChildTable,
            $this->authItemChildTableColumns,
            $this->prepareItemChildData()
        );
    }

    protected function prepareItemChildData(): array
    {
        $data = [];
        foreach ($this->permissionCreateMap as $route => $description) {
            foreach ($this->roleList as $role) {
                $data[] = [$role, $route];
            }
        }

        return $data;
    }
}
