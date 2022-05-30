<?php

namespace Bitrix24Api\EntitiesServices\Lists;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\Lists\ElementModel;

class Element extends BaseEntity
{
    protected string $method = 'lists.element.%s';
    public const ITEM_CLASS = ElementModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'get';

    public function get(int|string $id): ?AbstractModel
    {
        $params = $this->baseParams;
        $params['ELEMENT_ID'] = $id;

        $response = $this->api->request(sprintf($this->getMethod(), 'get'), $params);

        $class = static::ITEM_CLASS;
        $entity = new $class($response->getResponseData()->getResult()->getResultData());
        return !empty($response) ? $entity : null;
    }
}
