<?php

namespace Bitrix24Api\EntitiesServices\CRM;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Models\CRM\CatalogModel;

class Catalog extends BaseEntity
{
    protected string $method = 'crm.catalog.%s';
    public const ITEM_CLASS = CatalogModel::class;
    protected string $resultKey = '';
    protected string $listMethod = 'list';
}
