<?php

namespace Bitrix24Api\EntitiesServices\Task;

use Bitrix24Api\ApiClient;
use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\ApiException;
use Bitrix24Api\Exceptions\InvalidArgumentException;
use Bitrix24Api\Models\Task\CommentItemModel;

class CommentItem extends BaseEntity
{
    protected string $method = 'task.commentitem.%s';
    public const ITEM_CLASS = CommentItemModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'getlist';
    protected int $taskId;

    public function __construct(ApiClient $api, $params = [])
    {
        parent::__construct($api, $params);
        if (!array_key_exists('taskId', $params) || empty($params['taskId'])) {
            throw new InvalidArgumentException('taskId');
        } else {
            $this->taskId = $params['taskId'];
        }
    }

    public function get(int|string $id): ?CommentItemModel
    {
        $class = static::ITEM_CLASS;
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'get'), [$this->taskId, $id]);

            $result = $response->getResponseData()->getResult()->getResultData();
            if (isset($result['ID']))
                return new $class($result);
            else
                return null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function add(array $fields = []): bool|int
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'add'), [$this->taskId, $fields]);
            $id = $response->getResponseData()->getResult()->getResultData();
            $id = array_pop($id);
            if ($id > 0) {
                return $id;
            } else {
                return false;
            }
        } catch (ApiException $e) {

        }
        return false;
    }
}
