<?php

namespace Bitrix24Api\EntitiesServices\Entity;

use Bitrix24Api\ApiClient;
use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\EntitiesServices\Traits\GetTrait;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Exceptions\Entity\AlredyExists;
use Bitrix24Api\Exceptions\InvalidArgumentException;
use Bitrix24Api\Models\Entity\EntityModel;
use Illuminate\Support\Facades\Log;

class Entity extends BaseEntity
{
    protected string $method = 'entity.%s';
    public const ITEM_CLASS = EntityModel::class;
    protected string $resultKey = '';
    protected string $listMethod = '';
    private string $entityId = '';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(ApiClient $api, string $entityTypeId)
    {
        parent::__construct($api, []);
        if (empty($entityTypeId)) {
            throw new InvalidArgumentException('entityId is null');
        }
        $this->entityId = $entityTypeId;
    }
    /**
     * @throws \Exception
     */

    public function add(string $name, array $access = [])
    {
        $params = [
            'ENTITY' => $this->entityId,
            'NAME' => $name,
            'ACCESS' => $access
        ];

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

    public function get(): ?EntityModel
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'get'), ['ENTITY' => $this->entityId]);
            return new EntityModel($response->getResponseData()->getResult()->getResultData());
        }catch (ApiException $exception){
            return null;
        }
    }
}
