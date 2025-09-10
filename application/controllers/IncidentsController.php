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
        $host = $this->params->get('host');
        $service = $this->params->get('service');

        $this->addContent(Html::tag('h1', 'ServiceNow Incidents'));

        $db = $this->getDb();

        $incidents = Incident::on($db)
            ->filter(Filter::all(
                Filter::equal('service_name', $service),
                Filter::equal('host_name', $host)
            ));

        $this->addContent(new IncidentTable($incidents));
    }
}
