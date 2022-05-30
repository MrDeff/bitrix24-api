<?php

namespace Bitrix24Api\EntitiesServices\CRM;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Models\CRM\ActivityModel;

class Activity extends BaseEntity
{
    protected string $method = 'crm.activity.%s';
    public const ITEM_CLASS = ActivityModel::class;
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
}
