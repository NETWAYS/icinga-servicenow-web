<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Template;
use Icinga\Module\Servicenow\Widget\TemplateTable;

use ipl\Html\Attributes;
use ipl\Html\HtmlElement;
use ipl\Web\Compat\CompatController;
use ipl\Web\Widget\Link;

class TemplatesController extends CompatController
{
    use Database;

    public function indexAction()
    {
        $this->addTitleTab(t('ServiceNow Templates'));

        $db = $this->getDb();

        $templates = Template::on($db);

        $paginationControl = $this->createPaginationControl($templates);
        $limitControl = $this->createLimitControl();

        $this->addControl($paginationControl);
        $this->addControl($limitControl);

        // If there are more links we could move this to library/
        $quickActions = HtmlElement::create(
            'ul',
            Attributes::create(['class' => 'quick-actions']),
        );

        $quickActions->add(
            HtmlElement::create(
                'li',
                [],
                (new Link($this->translate('Add'), 'servicenow/template', Attributes::create([
                    'title' => $this->translate('Create a new template'),
                    'class' => 'icon-plus action-link',
                    'data-base-target' => '_next',
                ])))
            )
        );

        $this->addControl($quickActions);

        $this->addContent(new TemplateTable($templates));
    }
}
