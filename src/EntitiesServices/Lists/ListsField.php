<?php

namespace Bitrix24Api\EntitiesServices\Lists;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\EntitiesServices\Traits\GetWithoutParamsTrait;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Exceptions\Entity\AlredyExists;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\Lists\ListFieldModel;
use Bitrix24Api\Models\Lists\ListModel;

class ListsField extends BaseEntity
{
    use GetWithoutParamsTrait;

    protected string $method = 'lists.field.%s';
    public const ITEM_CLASS = ListFieldModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'get';

    /**
     *
     * @throws \Exception
     */
    public function get(string $iblockTypeId, $iblockCodeOrId, int $sonetGroupId = 0,string $fieldId = null)
    {
        $params = [
            'IBLOCK_TYPE_ID' => $iblockTypeId,
            'SOCNET_GROUP_ID' => $sonetGroupId,
        ];

        if (is_int($iblockCodeOrId)) {
            $params['IBLOCK_ID'] = $iblockCodeOrId;
        } else {
            $params['IBLOCK_CODE'] = $iblockCodeOrId;
        }

        if($fieldId)
        {
            $params['FIELD_ID'] = $fieldId;
        }

        $class = static::ITEM_CLASS;
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'get'), $params);
            if($fieldId) {
                return new $class(!empty($response->getResponseData()->getResult()->getResultData()) ? current($response->getResponseData()->getResult()->getResultData()) : []);
            }
            else{
                $result = [];
                foreach ($response->getResponseData()->getResult()->getResultData() as $field) {
                    $result[] = new $class($field);
                }
                return $result;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function add(string $iblockTypeId, string $iblockCode, int $sonetGroupId = 0, array $fields)
    {
        $params = [
            'IBLOCK_TYPE_ID' => $iblockTypeId,
            'IBLOCK_CODE' => $iblockCode,
            'SOCNET_GROUP_ID' => $sonetGroupId,
            'FIELDS' => $fields,
        ];

        if (!empty($this->baseParams))
            $params = array_merge($params, $this->baseParams);

        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'add'), $params);
            $result = $response->getResponseData()->getResult()->getResultData();
            $id = current($result);
            if (!empty($id)) {
                return $id;
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
}
