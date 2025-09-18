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
            $message = [$this->translate('Icinga ServiceNow Web is not connected to daemon'), $output];

            $this->setMessage(implode(': ', array_filter($message)));
            $this->setState(self::STATE_CRITICAL);

            return;
        }

        $details = json_decode($output, true);
        $database = $details['database'] ?? "";
        $snow = $details['snow'] ?? "";

        if ($database !== 'OK') {
            $message = [$this->translate('Icinga ServiceNow daemon is not connected to database'), $output];

            $this->setMessage(implode(': ', array_filter($message)));
            $this->setState(self::STATE_CRITICAL);

            return;
        }

        if ($snow !== 'OK') {
            $message = [$this->translate('Icinga ServiceNow daemon is not connected to ServiceNow'), $output];

            $this->setMessage(implode(': ', array_filter($message)));
            $this->setState(self::STATE_CRITICAL);

            return;
        }

        $message = [$this->translate('Icinga ServiceNow Web is connected to daemon'), $output];
        $this->setMessage(implode(': ', array_filter($message)));
        $this->setState(self::STATE_OK);
    }
}
