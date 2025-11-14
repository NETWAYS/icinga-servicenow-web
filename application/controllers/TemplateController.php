<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Template;
use Icinga\Module\Servicenow\Forms\TemplateForm;
use Icinga\Web\Notification;

use ipl\Web\Compat\CompatController;
use ipl\Html\Html;
use ipl\Stdlib\Filter;

// use Psr\Http\Message\ServerRequestInterface;

class TemplateController extends CompatController
{
    use Database;

    public function indexAction()
    {
        $id = $this->params->get('id');

        $this->addContent(Html::tag('h1', 'ServiceNow Template'));

        $db = $this->getDb();

        $template = Template::on($db)
            ->filter(Filter::equal('id', $id))
            ->first();

        // If we have a template, then render its fields
        $tf = new TemplateForm($db, $template);

        if ($template !== null) {
            $tf->populate(['display_name' => $template->display_name]);
            $tf->populate(['template_json' => $template->fields]);
        }

        $tf->on(TemplateForm::ON_SENT, function (TemplateForm $form) use ($id) {
            if ($form->hasBeenRemoved()) {
                $form->removeTemplate();
                Notification::success('Successfully removed template');
                $this->redirectNow('__CLOSE__');
            }
            if ($form->hasBeenSaved()) {
                $form->upsertTemplate();
                Notification::success('Successfully saved template');
                $this->redirectNow('__CLOSE__');
            }
        });

        $tf->handleRequest($this->getServerRequest());

        $this->addContent($tf);
    }
}
