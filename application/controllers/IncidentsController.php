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

        // This currently shows all incidents for the host.
        // Not sure if this is a bug or a feature.
        $f = Filter::any(
            Filter::equal('host_name', $host),
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

        $paginationControl = $this->createPaginationControl($incidents);

        $this->addControl($paginationControl);

        $this->addContent(new IncidentTable($incidents));
    }
}
