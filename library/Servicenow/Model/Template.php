<?php

namespace Icinga\Module\Servicenow\Model;

use Icinga\Util\Json;

use ipl\Orm\Model;

class Template extends Model
{
    public function getTableName(): string
    {
        return "template";
    }

    public function getKeyName(): string
    {
        return "id";
    }

    public function getColumns(): array
    {
        return [
            'id',
            'display_name',
            'fields',
        ];
    }

    public function getFields()
    {
        $f = [];

        try {
            $f = Json::decode($this->fields, true);
        } catch (JsonDecodeException $e) {
            // Nothing we can do
        }

        return $f;
    }
}
