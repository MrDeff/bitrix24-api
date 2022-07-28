<?php

namespace Bitrix24Api\EntitiesServices\Lists;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Exceptions\Entity\AlredyExists;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\Lists\ElementModel;

class Element extends BaseEntity
{
    protected string $method = 'lists.element.%s';
    public const ITEM_CLASS = ElementModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'get';

    public function get($id): ?AbstractModel
    {
        $params = $this->baseParams;
        $params['ELEMENT_ID'] = $id;

        $response = $this->api->request(sprintf($this->getMethod(), 'get'), $params);

        $class = static::ITEM_CLASS;
        $entity = new $class($response->getResponseData()->getResult()->getResultData());
        return !empty($response) ? $entity : null;
    }

    public function add(string $iblockTypeId, $iblockCodeOrId, string $elementCode, string $listElementUrl, array $fields, int $sonetGroupId = 0)
    {
        $params = [
            'IBLOCK_TYPE_ID' => $iblockTypeId,
            'ELEMENT_CODE' => $elementCode,
            'LIST_ELEMENT_URL' => $listElementUrl,
            'FIELDS' => $fields,
            'SOCNET_GROUP_ID' => $sonetGroupId
        ];

        if (is_int($iblockCodeOrId)) {
            $params['IBLOCK_ID'] = $iblockCodeOrId;
        } else {
            $params['IBLOCK_CODE'] = $iblockCodeOrId;
        }

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
            throw new \Exception($e->getMessage());
        }
    }
}
