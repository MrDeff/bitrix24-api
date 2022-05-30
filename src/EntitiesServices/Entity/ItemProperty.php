<?php

namespace Bitrix24Api\EntitiesServices\Entity;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Exceptions\Entity\AlredyExists;
use Bitrix24Api\Models\Entity\ItemPropertyModel;

class ItemProperty extends BaseEntity
{
    protected string $method = 'entity.item.property.%s';
    public const ITEM_CLASS = ItemPropertyModel::class;
    protected string $resultKey = '';
    protected string $listMethod = '';

    public function add(string $property, string $name, string $type = 'S'): bool|int
    {
        $params = [
            'PROPERTY' => $property,
            'NAME' => $name,
            'TYPE' => $type
        ];

        if (!empty($this->baseParams))
            $params = array_merge($params, $this->baseParams);

        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'add'), $params);
            $result = $response->getResponseData()->getResult()->getResultData();
            if (current($result) === true) {
                return true;
            } else {
                return false;
            }
        } catch (ApiException $e) {
            if ($e->getTitle() === 'ERROR_ENTITY_ALREADY_EXISTS') {
                throw new AlredyExists($e->getTitle(), 0, $e->getDescription());
            } else {
                throw new \Exception($e->getMessage());
            }
        }
    }

    public function get($property): ?ItemPropertyModel
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'get'), ['ENTITY' => $this->baseParams['ENTITY'], 'PROPERTY' => $property]);
            return new ItemPropertyModel($response->getResponseData()->getResult()->getResultData());
        } catch (ApiException $exception) {
            return null;
        }
    }
}
