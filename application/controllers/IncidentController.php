<?php

namespace Icinga\Module\Servicenow\Controllers;

use ipl\Web\Compat\CompatController;
use ipl\Html\Html;

class IncidentController extends CompatController
{
    public function indexAction()
    {
        $id = $this->params->get('id');

        $this->addContent(Html::tag('h1', 'ServiceNow Incident'));
    }
}
