<?php

namespace Bitrix24Api\EntitiesServices\Entity;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Models\Entity\ItemModel;

class Item extends BaseEntity
{
    protected string $method = 'entity.item.%s';
    public const ITEM_CLASS = ItemModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'get';

    public function add($params = []): bool|int
    {
        if (!empty($this->baseParams))
            $params = array_merge($params, $this->baseParams);

        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'add'), $params);
            $result = $response->getResponseData()->getResult()->getResultData();
            $id = current($result);
            if ($id > 0) {
                return $id;
            } else {
                return false;
            }
        } catch (ApiException $e) {

        }
        return false;
    }

    /**
     * @throws \Exception
     */
    public function update($id, array $fields): bool
    {
        if (!empty($this->baseParams))
            $fields = array_merge($fields, $this->baseParams);

        $fields['ID'] = $id;

        try {
            $this->api->request(sprintf($this->getMethod(), 'update'), $fields);
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
