<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Widget\ModuleSetup;

use ipl\Web\Compat\CompatController;

class SetupController extends CompatController
{
    public function indexAction(): void
    {
        $this->addTitleTab('Setup');

        $setup = new ModuleSetup($this->getServerRequest());

        $this->addContent($setup);
    }
}
