<?php

namespace Bitrix24Api\EntitiesServices\Entity;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Exceptions\Entity\AlredyExists;
use Bitrix24Api\Models\Entity\EntityModel;
use Illuminate\Support\Facades\Log;

class Entity extends BaseEntity
{
    protected string $method = 'entity.%s';
    public const ITEM_CLASS = EntityModel::class;
    protected string $resultKey = '';
    protected string $listMethod = '';

    /**
     * @throws \Exception
     */

    public function add(string $name, array $access = []): bool|int
    {
        $params = [
            'NAME' => $name,
            'ACCESS' => $access
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

    public function get($id = 0): ?EntityModel
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'get'), ['ENTITY' => $this->baseParams['ENTITY']]);
            return new EntityModel($response->getResponseData()->getResult()->getResultData());
        }catch (ApiException $exception){
            return null;
        }
    }
}
