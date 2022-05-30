<?php

namespace Bitrix24Api\EntitiesServices\CRM;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Models\CRM\CompanyModel;

class Company extends BaseEntity
{
    protected string $method = 'crm.company.%s';
    public const ITEM_CLASS = CompanyModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    /**
     * @throws \Exception
     */
    public function update($id, array $fields, $params = []): bool
    {
        try {
            $this->api->request(sprintf($this->getMethod(), 'update'), ['id' => $id, 'fields' => $fields, 'params' => $params]);
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
