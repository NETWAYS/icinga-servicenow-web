<?php

namespace Icinga\Module\Servicenow\Clicommands;

use Icinga\Module\Servicenow\Client\Snow;

use Icinga\Application\Logger;
use Icinga\Cli\Command;

use Exception;

class SendCommand extends Command
{
    /**
     * Create a notification for the given Host or Service
     *
     * Use this as a NotificationCommand for Icinga
     *
     * USAGE
     *
     * icingacli servicenow send notification [options]
     *
     * REQUIRED OPTIONS
     *
     *   --state <icinga-notifiaction-state>
     *   --type <icinga-notifiaction-type>
     *   --output <icinga-notifiaction-output>
     *   --name <icinga-notifiaction-name>
     *   --host <host-name> Icinga Host name
     *   --template <service-now-template>
     *
     * OPTIONAL
     *
     *   --service <service-name>     Icinga Service name
     *   --is-volatile <true|false>   Is this a volatile Host or Service (default: false)
     */
    public function notificationAction(): void
    {
        $notificationState = $this->params->getRequired('state');
        $notificationType = $this->params->getRequired('type');
        $notificationOutput = $this->params->getRequired('output');
        $notificationName = $this->params->getRequired('name');
        $hostName = $this->params->getRequired('host');
        $template = $this->params->getRequired('template');

        // Default to ""
        $serviceName = $this->params->get('service', "");

        // Default to false
        $isVolative = $this->params->get('is-volatile', false);

        try {
            $client = Snow::fromConfig();
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            exit(1);
        }

        try {
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
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            exit(1);
        }
    }
}
