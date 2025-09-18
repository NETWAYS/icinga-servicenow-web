<?php

namespace Icinga\Module\Servicenow\ProvidedHook\Monitoring;

use Icinga\Module\Monitoring\Hook\HostActionsHook;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Web\Url;

class HostActions extends HostActionsHook
{
    /**
     * @param Host $host
     * @return array
     * @throws \Icinga\Exception\ProgrammingError
     */
    public function getActionsForHost(Host $host)
    {
        return [
            'ServiceNow Incidents' => Url::fromPath(
                'servicenow/incidents',
                [
                    'host_name' => $host->host_name,
                ]
            )
        ];
    }
}
