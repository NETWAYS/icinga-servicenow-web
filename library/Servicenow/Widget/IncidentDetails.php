<?php

namespace Icinga\Module\Servicenow\Widget;

use Icinga\Module\Servicenow\ProvidedHook\Icingadb\IcingadbSupport;

use Icinga\Application\Modules\Module;

use ipl\Html\Table;
use ipl\Html\Html;
use ipl\Web\Url;
use ipl\Web\Widget\Link;

use ipl\I18n\Translation;

class IncidentDetails extends Table
{
    use Translation;

    protected $defaultAttributes = [
        'class' => 'name-value-table'
    ];

    protected $incident;

    public function __construct($incident)
    {
        $this->incident = $incident;
    }

    protected function addKeyValue($key, $value)
    {
        $tbody = $this->getBody();
        $r = Table::row([$key, $value]);
        $tbody->addHtml($r);
    }

    /** generateObjectLink generates a link depending on the object
     *  and IcingaDB or Monitoring module.
     *
     * icingadb/host?name=HOST
     * monitoring/host/show?host=HOST
     * icingadb/service?name=SERVICE&host.name=HOST
     * monitoring/service/show?host=HOST&service=SERVICE
    */
    protected function generateObjectLink($host_name, $service_name = null)
    {
        if (Module::exists('icingadb') && IcingadbSupport::useIcingaDbAsBackend()) {
            $text = $host_name;
            $basePath = 'icingadb/';
            $objectPath = 'host/';
            $params = ['name' => $host_name];

            if (isset($service_name)) {
                $text = $service_name;
                $objectPath = 'service/';
                $params = ['host.name' => $host_name, 'name' => $service_name];
            }
            $path = $basePath . $objectPath;

            return Html::tag('span')->add(
                new Link($text, Url::fromPath($path, $params), ['class' => 'action-link'])
            );
        }

        $text = $host_name;
        $basePath = 'monitoring/';
        $objectPath = 'host/';
        $params = ['host' => $host_name];

        if (isset($service_name)) {
            $text = $service_name;
            $objectPath = 'service/';
            $params = ['host' => $host_name, 'service' => $service_name];
        }

        $path = $basePath . $objectPath;

        return Html::tag('span')->add(
            new Link($text, Url::fromPath($path, $params))
        );
    }

    protected function assemble()
    {
        $tbody = $this->getBody();

        $this->addKeyValue($this->translate('Incident'), $this->incident->incident_number);
        $this->addKeyValue($this->translate('Created'), $this->incident->db_created_at);
        $this->addKeyValue($this->translate('Type'), $this->incident->notification_type);
        $this->addKeyValue($this->translate('State'), $this->incident->notification_state);

        $this->addKeyValue($this->translate('Host'), $this->generateObjectLink($this->incident->host_name));

        if (isset($this->incident->service_name)) {
            $this->addKeyValue($this->translate('Service'), $this->generateObjectLink($this->incident->host_name, $this->incident->service_name));
        }
    }
}
