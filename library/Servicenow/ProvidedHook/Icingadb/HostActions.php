<?php

namespace Icinga\Module\Servicenow\ProvidedHook\Icingadb;

use Icinga\Module\Icingadb\Hook\HostActionsHook;
use Icinga\Module\Icingadb\Model\Host;

use ipl\Web\Url;
use ipl\Web\Widget\Link;

class HostActions extends HostActionsHook
{
    public function getActionsForObject(Host $host): array
    {
        return [
            new Link(
                'ServiceNow Incidents',
                Url::fromPath(
                    'servicenow/incidents',
                    [
                        'host_name' => $host->name
                    ]
                )
            )
        ];
    }
}
