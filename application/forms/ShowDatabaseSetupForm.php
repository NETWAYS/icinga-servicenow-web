<?php

namespace Icinga\Module\Servicenow\Forms;

use Icinga\Data\ResourceFactory;

use ipl\Web\Compat\CompatForm;
use ipl\Web\Url;
use ipl\Html\Text;
use ipl\Html\Html;
use ipl\Web\Widget\Link;

class ShowDatabaseSetupForm extends CompatForm
{
    protected function assemble(): void
    {
        $elements = $this->createElements();
        $this->setRedirectUrl(Url::fromPath('servicenow/incidents'));
        $this->add($elements);
    }

    public function createElements()
    {
        $dbResources = ResourceFactory::getResourceConfigs('db')->keys();

        $desc = Html::tag('span')->add(
            [
                new Text(t('Icinga ServiceNow requires a database resource to connect to the database. To create a new resource for the database navigate to: ')),
                new Link('Configuration - Application - Resources', Url::fromPath('config/resource/'))
            ]
        );

        $this->add($desc);

        $this->addElement('select', 'db_resource', [
            'description' => t('The database resource for the Icinga ServiceNow module'),
            'label' => t('Database Resource'),
            'multiOptions' => array_merge(
                ['' => sprintf(' - %s - ', t('Please choose'))],
                array_combine($dbResources, $dbResources)
            ),
            'disable' => [''],
            'required' => true,
            'value' => ''
        ]);

        $this->addElement('submit', 'store_configuration', ['label' => t('Store Configuration')]);
    }
}
