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
     *   --service <service-name>    Icinga Service name
     *   --is-volatile <true|false>  Is this a volatile Host or Service (default: false)
     *   --extra <key=value>       Additional key-value fields to send, like --extra description=value, keep in mind --extra="key=value" is not allowed
     *                             example: --extra "description=This is a description" --extra "host=\$host.name\$"
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

        // Defaults to []
        $extras = $this->params->get('extra');
        if (!is_array($extras)) {
            Logger::error("invalid parameter " . $extras . "; --extra requires key=value as input");
            $this->showUsage('notification');
            exit(1);
        }
        $additionalFields = $this->getAdditionalFields($extras);

        try {
            $client = Snow::fromConfig();
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            exit(1);
        }

        try {
            $client->send(
                hostName: $hostName,
                serviceName: $serviceName,
                isVolatile: $isVolative,
                notificationName: $notificationName,
                notificationType: $notificationType,
                notificationState: $notificationState,
                notificationOutput: $notificationOutput,
                template: $template,
                additionalFields: $additionalFields,
            );
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            exit(1);
        }
    }

    /**
     * getAdditionalFields extracts all extra fields from
     * the parameters and returns them as an array.
     * icingacli does not have repeated parameters, so
     * we have to use a prefix and extract the key
     */
    protected function getAdditionalFields(array $params): array
    {
        $extras = [];
        foreach ($params as $param) {
            $p = explode('=', $param);
            if (count($p) < 2) {
                Logger::error("invalid parameter " . $param . "; --extra requires key=value as input");
                exit(1);
            }
            $extras[$p[0]] = $p[1];
        }
        return $extras;
    }
}
