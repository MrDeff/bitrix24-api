<?php

namespace Bitrix24Api\EntitiesServices\Disk;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\MethodNotFound;
use Bitrix24Api\Models\AbstractModel;
use Bitrix24Api\Models\Disk\FileModel;
use Bitrix24Api\Models\Disk\FolderModel;

class Folder extends BaseEntity
{
    protected string $method = 'disk.folder.%s';
    public const ITEM_CLASS = FolderModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    public function uploadFile(int $id, string $fileContent, array $data): ?FileModel
    {
        try {
            $response = $this->api->request(sprintf($this->getMethod(), 'uploadfile'), ['id' => $id, 'fileContent' => $fileContent, 'data' => $data]);
            return new FileModel($response->getResponseData()->getResult()->getResultData());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws MethodNotFound
     */
    public function getList(array $params = []): \Generator
    {
        throw new MethodNotFound();
    }

    /**
     * @throws MethodNotFound
     */
    public function getListFast(array $params = []): \Generator
    {
        throw new MethodNotFound();
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
