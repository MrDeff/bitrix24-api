<?php

namespace Bitrix24Api\EntitiesServices\CRM;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Exceptions\NotImplement;
use Bitrix24Api\Models\CRM\ProductPropertyModel;

class ProductProperty extends BaseEntity
{
    protected string $method = 'crm.product.property.%s';
    public const ITEM_CLASS = ProductPropertyModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';

    /**
     * @throws NotImplement
     */
    public function add(array $fields): bool
    {
        throw new NotImplement();
    }
}
