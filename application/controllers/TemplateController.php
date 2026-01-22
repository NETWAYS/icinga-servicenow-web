<?php

namespace Icinga\Module\Servicenow\Controllers;

use Icinga\Module\Servicenow\Common\Database;
use Icinga\Module\Servicenow\Model\Template;
use Icinga\Module\Servicenow\Forms\TemplateForm;
use Icinga\Web\Notification;

use ipl\Web\Compat\CompatController;
use ipl\Stdlib\Filter;
use PDOException;

class TemplateController extends CompatController
{
    use Database;

    public function init(): void
    {
        $this->assertPermission('servicenow/template/edit');
    }

    public function indexAction()
    {
        $id = $this->params->get('id');

        $this->addTitleTab(t('ServiceNow Template'));

        $db = $this->getDb();

        $template = Template::on($db)
            ->filter(Filter::equal('id', $id))
            ->first();

        $tf = new TemplateForm($db);
        // If we have a template, then render its fields
        if (isset($template)) {
            $tf->load($template);
        }

        $tf
            ->on(TemplateForm::ON_SENT, function (TemplateForm $form) {
                if ($form->hasBeenRemoved()) {
                    $form->removeTemplate();
                    Notification::success('Template has been removed');
                    $this->redirectNow('__CLOSE__');
                }
                if ($form->hasBeenSaved()) {
                    try {
                        $form->upsertTemplate();
                        Notification::success('Template has been stored');
                        $this->redirectNow('__CLOSE__');
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            $displayName = $form->getValue('display_name');
                            Notification::error(t(sprintf('Template with name %s already exists', $displayName)));
                        } else {
                            Notification::error($e->getMessage());
                        }
                    }
                }
            })->handleRequest($this->getServerRequest());

        $this->addContent($tf);
    }
}
