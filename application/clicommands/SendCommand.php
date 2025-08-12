<?php

namespace Icinga\Module\Servicenow\Clicommands;

use Icinga\Cli\Command;

use Icinga\Module\Servicenow\Client\Snow;
// use Icinga\Application\Icinga;
// use Icinga\Application\Logger;
// use Icinga\Application\Config;
// use Icinga\Exception\IcingaException;
// use Exception;

class SendCommand extends Command
{
    /**
     * Create an issue for the given Host or Service problem
     *
     * Use this as a NotificationCommand for Icinga
     *
     * USAGE
     *
     * icingacli servicenow send notification [options]
     *
     * REQUIRED OPTIONS
     *
     *   --table <table-name>         ServiceNow
     *
     * OPTIONAL
     *
     *   --service <service-name>   Icinga Service name
     */
    public function notificationAction(): void
    {
        $notificationState = $this->params->getRequired('state');
        $notificationType = $this->params->getRequired('type');
        $notificationOutput = $this->params->getRequired('output');
        $notificationName = $this->params->getRequired('name');
        $hostName = $this->params->getRequired('host');
        $template = $this->params->getRequired('template');

        $serviceName = $this->params->get('service');
        // Default to false
        $isVolative = $this->params->get('is-volatile');

        $client = Snow::fromConfig();

        $client->send(
            $hostName,
            $serviceName,
            $isVolative,
            $notificationName,
            $notificationType,
            $notificationState,
            $notificationOutput,
            $template
        );
    }
}
