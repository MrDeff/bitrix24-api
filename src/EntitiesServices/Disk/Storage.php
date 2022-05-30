<?php

namespace Bitrix24Api\EntitiesServices\Disk;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\MethodNotFound;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\Disk\FolderModel;
use Bitrix24Api\Models\Disk\StorageModel;
use Illuminate\Support\Facades\Log;

class Storage extends BaseEntity
{
    protected string $method = 'disk.storage.%s';
    public const ITEM_CLASS = StorageModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'getlist';

    /**
     * @return StorageModel|null
     * @throws \Exception
     */
    public function getForApp(): ?AbstractModel
    {
        $class = static::ITEM_CLASS;
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'getforapp'), []);
            return new $class($response->getResponseData()->getResult()->getResultData());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function addFolder(int $id, array $data): ?FolderModel
    {
        $class = static::ITEM_CLASS;
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'addfolder'), ['id' => $id, 'data' => $data]);
            return new FolderModel($response->getResponseData()->getResult()->getResultData());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws MethodNotFound
     */
    public function update($id, array $fields): bool
    {
        throw new MethodNotFound();
    }

    /**
     * @throws MethodNotFound
     */
    public function delete($id): bool
    {
        throw new MethodNotFound();
    }
}
