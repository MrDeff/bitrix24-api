<?php

namespace Bitrix24Api\Models\CRM\Smart;

use Bitrix24Api\Models\BaseApiModel;
use Bitrix24Api\Models\Interfaces\HasIdInterface;

class ItemModel extends BaseApiModel implements HasIdInterface
{

    public function toArray(): array
    {
        return $this->data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getXmlId(): ?string
    {
        return $this->xmlId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getEntityTypeId(): ?string
    {
        return $this->entityTypeId;
    }
}
