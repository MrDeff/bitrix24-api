<?php

namespace Bitrix24Api\EntitiesServices\Task;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\NotImplement;
use Bitrix24Api\Models\Task\ItemModel;

class Item extends BaseEntity
{
    protected string $method = 'task.item.%s';
    public const ITEM_CLASS = ItemModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    public function get(int|string $id, array $select = []): ?ItemModel
    {
        throw new NotImplement();
    }

    public function add(array $fields = []): bool|int
    {
        throw new NotImplement();
    }

    public function getFiles(int $taskId): array
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'getfiles'), ['TASKID' => $taskId]);
            return $response->getResponseData()->getResult()->getResultData() ?? [];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function addFile(int $taskId, string $fileName, string $fileContent): ?array
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'addfile'), ['TASKID' => $taskId, 'FILE' => [
                'NAME' => $fileName,
                'CONTENT' => base64_encode($fileContent)
            ]]);
            return $response->getResponseData()->getResult()->getResultData() ?? null;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
