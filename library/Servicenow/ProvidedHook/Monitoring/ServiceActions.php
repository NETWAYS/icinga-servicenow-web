<?php

namespace Icinga\Module\Servicenow\ProvidedHook\Monitoring;

use Icinga\Module\Monitoring\Hook\ServiceActionsHook;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;

class ServiceActions extends ServiceActionsHook
{
    /**
     * @param Service $service
     * @return array
     * @throws \Icinga\Exception\ProgrammingError
     */
    public function getActionsForService(Service $service)
    {
        return [
            'ServiceNow Incidents' => Url::fromPath(
                'servicenow/incidents',
                [
                    'host_name' => $service->host_name,
                    'service_name' => $service->service_description,
                ]
            )
        ];
    }
}
