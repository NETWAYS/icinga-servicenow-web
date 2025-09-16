<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Incident;
use Icinga\Module\Servicenow\Widget\IncidentDetails;

use ipl\Web\Compat\CompatController;
use ipl\Html\Html;
use ipl\Stdlib\Filter;

class IncidentController extends CompatController
{
    use Database;

    public function indexAction()
    {
        $id = $this->params->get('id');

        $this->addContent(Html::tag('h1', 'ServiceNow Incident'));

        $db = $this->getDb();

        $incident = Incident::on($db)
            ->filter(Filter::equal('id', $id))
            ->first();

        if (empty($incident)) {
            $this->httpNotFound($this->translate('Incident not found'));
        }

        $this->addContent(new IncidentDetails($incident));
    }
}
