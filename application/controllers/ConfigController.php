<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Forms\SnowConfigForm;
use Icinga\Module\Servicenow\Forms\DatabaseConfigForm;

use Icinga\Application\Config;
use Icinga\Web\Form;
use Icinga\Web\Widget\Tab;
use Icinga\Web\Widget\Tabs;

use ipl\Web\Compat\CompatController;
use ipl\Html\HtmlString;

class ConfigController extends CompatController
{
    public function init()
    {
        $this->assertPermission('config/modules');

        parent::init();
    }

    public function databaseAction()
    {
        $form = (new DatabaseConfigForm())
            ->setIniConfig(Config::module('servicenow'));

        $form->handleRequest();

        $this->mergeTabs($this->Module()->getConfigTabs()->activate('database'));

        $this->addFormToContent($form);
    }

    public function daemonAction()
    {
        $form = (new SnowConfigForm())
            ->setIniConfig(Config::module('servicenow'));

        $form->handleRequest();

        $this->mergeTabs($this->Module()->getConfigTabs()->activate('daemon'));

        $this->addFormToContent($form);
    }

    protected function addFormToContent(Form $form)
    {
        $this->addContent(new HtmlString($form->render()));
    }

    protected function mergeTabs(Tabs $tabs): self
    {
        /** @var Tab $tab */
        foreach ($tabs->getTabs() as $tab) {
            $this->tabs->add($tab->getName(), $tab);
        }

        return $this;
    }
}
