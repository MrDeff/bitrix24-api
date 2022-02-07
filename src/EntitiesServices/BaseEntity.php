<?php

namespace Bitrix24Api\EntitiesServices;

use Bitrix24Api\ApiClient;
use Bitrix24Api\Models\BaseApiModel;

abstract class BaseEntity
{
    public const ITEM_CLASS = '';
    protected string $method = '';
    protected ApiClient $api;
    protected string $resultKey = '';

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function call(array $params = []): ?BaseApiModel
    {
        $response = $this->api->request($this->getMethod(), $params);

        $class = static::ITEM_CLASS;
        $entity = new $class([]);
        return !empty($response) ? $entity->fromArray($response->getResponseData()->getResult()->getResultData()) : null;
    }

    public function get(int $id): ?BaseApiModel
    {
        $response = $this->api->request(sprintf($this->getMethod(), 'get'), ['id' => $id]);

        $class = static::ITEM_CLASS;
        $entity = new $class($response->getResponseData()->getResult()->getResultData());
        return !empty($response) ? $entity : null;
    }

    public function getList(array $params = []): \Generator
    {
        $method = sprintf($this->getMethod(), 'list');
        do {

            $result = $this->api->request(
                $method,
                $params
            );

            if($this->resultKey){
                $resultData = $result->getResponseData()->getResult()->getResultData()[$this->resultKey] ?? [];
            }
            else{
                $resultData = $result->getResponseData()->getResult()->getResultData() ?? [];
            }

            $start = $params['start'] ?? 0;
            $this->api->getLogger()?->debug(
                "По запросу (getList) {$method} (start: {$start}) получено сущностей: " . count($resultData) .
                ", всего существует: " . $result->getResponseData()->getPagination()->getTotal(),
            );

            $class = static::ITEM_CLASS;
            foreach ($resultData as $resultDatum) {
                yield new $class($resultDatum);
            }

            if (empty($result->getResponseData()->getPagination()->getNextItem())) {
                break;
            }

            $params['start'] = $result->getResponseData()->getPagination()->getNextItem();
        } while (true);
    }

    public function getListFast(array $params = []): \Generator
    {
        $method = sprintf($this->getMethod(), 'list');
        $params['order']['id'] = 'ASC';
        $params['filter']['>id'] = 0;
        $params['start'] = -1;

        $totalCounter = 0;

        do {
            $result = $this->api->request(
                $method,
                $params
            );

            if($this->resultKey){
                $resultData = $result->getResponseData()->getResult()->getResultData()[$this->resultKey] ?? [];
            }
            else{
                $resultData = $result->getResponseData()->getResult()->getResultData() ?? [];
            }

            $start = $params['start'] ?? 0;
            $resultCounter = count($resultData);
            $totalCounter += $resultCounter;
            $this->api->getLogger()?->debug(
                "По запросу (getListFast) {$method} (start: {$start}) получено сущностей: " . $resultCounter .
                ", всего получено: " . $totalCounter,
            );

            $class = static::ITEM_CLASS;
            foreach ($resultData as $resultDatum) {
                yield new $class($resultDatum);
            }

            if ($resultCounter < 50) {
                break;
            }

            $params['filter']['>id'] = (new $class($resultData[ $resultCounter - 1 ]))->getId();
        } while (true);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
}
