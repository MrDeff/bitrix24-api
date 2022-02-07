<?php

namespace Bitrix24Api\EntitiesServices;

use Bitrix24Api\Models\ProfileModel;

class Profile extends BaseEntity
{
    protected string $method = 'profile';
    public const ITEM_CLASS = ProfileModel::class;
}