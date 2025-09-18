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
            'incident_number',
            'host_name',
            'service_name',
            'is_volatile',
            'notification_name',
            'notification_output',
            'notification_type',
            'notification_state',
            'template',
            'db_created_at',
            'db_last_updated_at',
        ];
    }

    public function getSearchColumns()
    {
        return ['sys_id', 'incident_number', 'host_name', 'service_name', 'notification_type'];
    }

    public function getColumnDefinitions()
    {
        return ['sys_id' => 'SysID',
                'incident_number' => 'Number',
                'host_name' => 'Host',
                'service_name' => 'Service',
                'notification_type' => 'Type',
        ];
    }
}
