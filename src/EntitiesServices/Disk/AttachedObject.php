<?php

namespace Bitrix24Api\EntitiesServices\Disk;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\MethodNotFound;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\Disk\AttachedObjectModel;

class AttachedObject extends BaseEntity
{
    protected string $method = 'disk.attachedObject.%s';
    public const ITEM_CLASS = AttachedObjectModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    public function get(int|string $id): ?AttachedObjectModel
    {
        return parent::get($id);
    }

    /**
     * @throws MethodNotFound
     */
    public function getList(array $params = []): \Generator
    {
        throw new MethodNotFound();
    }

    /**
     * @throws MethodNotFound
     */
    public function getListFast(array $params = []): \Generator
    {
        throw new MethodNotFound();
    }

    /**
     * @throws MethodNotFound
     */
    public function update($id, array $fields): bool
    {
        throw new MethodNotFound();
    }

    /**
     * @throws MethodNotFound
     */
    public function delete($id): bool
    {
        throw new MethodNotFound();
    }
}
