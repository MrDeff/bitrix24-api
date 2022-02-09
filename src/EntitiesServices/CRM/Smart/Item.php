<?php
namespace Bitrix24Api\EntitiesServices\CRM\Smart;

use Bitrix24Api\EntitiesServices\BaseEntity;
use Bitrix24Api\Models\CRM\Smart\ElementModel;

class Item extends BaseEntity
{
    protected string $method = 'crm.item.%s';
    public const ITEM_CLASS = ElementModel::class;
    protected string $resultKey = 'items';
}
