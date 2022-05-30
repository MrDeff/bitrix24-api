<?php

namespace Bitrix24Api\EntitiesServices\CRM;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Models\CRM\LeadModel;

class Lead extends BaseEntity
{
    protected string $method = 'crm.lead.%s';
    public const ITEM_CLASS = LeadModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    public function add(array $fields, $params = []): bool
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'add'), ['fields' => $fields, 'params' => $params]);
            $result = $response->getResponseData()->getResult()->getResultData();
            $id = $result['ID'];
            if ($id > 0) {
                return $id;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

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
