<?php

namespace Triangulum\Yii\Unit\Data\Db\Migration;

use yii\db\Migration;

class RbacMigration extends Migration
{
    protected array  $roleList                  = ['root'];
    protected int    $authType                  = 2;
    protected string $authItemTable             = 'auth_item';
    protected string $authItemChildTable        = 'auth_item_child';
    protected array  $authItemColumns           = [
        'name',
        'type',
        'description',
        'created_at',
        'updated_at',
    ];
    protected array  $authItemChildTableColumns = [
        'parent',
        'child',
    ];

    protected array $authItemData = [];

    public function safeDown()
    {
        $this->delete($this->authItemTable, [
            'IN',
            'name',
            array_keys($this->authItemData),
        ]);
    }

    public function safeUp()
    {
        $this->batchInsert(
            $this->authItemTable,
            $this->authItemColumns,
            $this->prepareItemData()
        );

        $this->batchInsert(
            $this->authItemChildTable,
            $this->authItemChildTableColumns,
            $this->prepareItemChildData()
        );
    }

    protected function prepareItemData(): array
    {
        $t = time();
        $data = [];
        foreach ($this->authItemData as $route => $description) {
            $data[] = [$route, $this->authType, $description, $t, $t];
        }

        return $data;
    }

    protected function prepareItemChildData(): array
    {
        $data = [];
        foreach ($this->authItemData as $route => $description) {
            foreach ($this->roleList as $role) {
                $data[] = [$role, $route];
            }
        }

        return $data;
    }
}
