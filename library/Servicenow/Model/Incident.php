<?php

namespace Icinga\Module\Servicenow\Model;

use ipl\Orm\Model;

class Incident extends Model
{
    public function getTableName(): string
    {
        return "incident";
    }

    public function getKeyName(): string
    {
        return "id";
    }

    public function getColumns(): array
    {
        return [
            'id',
            'sys_id',
            'host_name',
            'service_name',
            'notification_type',
            'notification_state',
            'notification_output',
            'created_at',
            'last_updated_at'
        ];
    }

    public function getSearchColumns()
    {
        return ['id'];
    }
}
