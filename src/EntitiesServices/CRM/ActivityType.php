<?php

namespace Bitrix24Api\EntitiesServices\CRM;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\MethodNotFound;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\CRM\ActivityTypeModel;

class ActivityType extends BaseEntity
{
    protected string $method = 'crm.activity.type.%s';
    public const ITEM_CLASS = ActivityTypeModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    /**
     * @throws \Exception
     */

    public function add(array $fields): bool
    {
        try {
            $this->api->request(sprintf($this->getMethod(), 'add'), ['fields' => $fields]);
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
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
    public function get(int|string $id): ?AbstractModel
    {
        throw new MethodNotFound();
    }

    /**
     * @throws MethodNotFound
     */
    public function fields(): array
    {
        throw new MethodNotFound();
    }
}
