<?php

namespace Icinga\Module\Servicenow\ProvidedHook;

use Icinga\Module\Servicenow\Client\Snow;

use Icinga\Application\Hook\HealthHook;

use ipl\I18n\Translation;

class SnowHealth extends HealthHook
{
    use Translation;

    public function getName(): string
    {
        return 'ServiceNow Daemon';
    }

    public function checkHealth(): void
    {
        $client = Snow::fromConfig();

        $status = $client->status();

        $output = $status['output'] ?? '';

        if (isset($status['error'])) {
            $message = [$this->translate('Icinga ServiceNow is not connected to Daemon'), $output];

            $this->setMessage(implode(': ', array_filter($message)));
            $this->setState(self::STATE_CRITICAL);

            return;
        }

        $details = json_decode($output, true);
        $database = $details['database'] ?? "";

        if ($database == 'OK') {
            $this->setState(self::STATE_OK);
        } else {
            $this->setState(self::STATE_WARNING);
        }

        $message = [$this->translate('Icinga ServiceNow connected to Daemon'), $output];
        $this->setMessage(implode(': ', array_filter($message)));
    }
}
