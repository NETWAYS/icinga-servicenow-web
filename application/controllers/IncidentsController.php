<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Incident;
use Icinga\Module\Servicenow\Widget\IncidentTable;

use ipl\Web\Compat\CompatController;
use ipl\Html\Html;
use ipl\Stdlib\Filter;

class IncidentsController extends CompatController
{
    use Database;

    public function indexAction()
    {
        $host = $this->params->getRequired('host');
        $service = $this->params->get('service');

        $this->addContent(Html::tag('h1', 'ServiceNow Incidents'));

        $db = $this->getDb();

        $f = Filter::all(
            Filter::equal('host_name', $host),
            Filter::unlike('service_name', '*')
        );

        if (isset($service)) {
            $f = Filter::all(
                Filter::equal('host_name', $host),
                Filter::equal('service_name', $service)
            );
        }

        $incidents = Incident::on($db)
            ->filter($f)
            ->orderBy('db_created_at', SORT_ASC);

        $this->addContent(new IncidentTable($incidents));
    }
}
