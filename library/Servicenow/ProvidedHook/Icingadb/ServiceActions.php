<?php

namespace Icinga\Module\Servicenow\ProvidedHook\Icingadb;

use Icinga\Module\Icingadb\Hook\ServiceActionsHook;
use Icinga\Module\Icingadb\Model\Service;

use ipl\Web\Url;
use ipl\Web\Widget\Link;

class ServiceActions extends ServiceActionsHook
{
    public function getActionsForObject(Service $service): array
    {
        return [
            new Link(
                'ServiceNow Incidents',
                Url::fromPath(
                    'servicenow/incidents',
                    [
                        'service' => $service->name,
                        'host' => $service->host->name,
                        'all' => true
                    ]
                )
            )
        ];
    }
}
