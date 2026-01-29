<?php

namespace Icinga\Module\Servicenow\Widget;

use Icinga\Module\Servicenow\Forms\ShowDatabaseSetupForm;

use Icinga\Application\Config;
use Icinga\Application\Icinga;
use Icinga\Web\Notification;
use Icinga\Web\Url;

use ipl\Html\BaseHtmlElement;
use ipl\Html\Html;

class ModuleSetup extends BaseHtmlElement
{
    protected $tag = 'div';
    protected $request;
    protected $defaultAttributes = ['class' => 'widget'];

    public function __construct($request)
    {
        $this->request = $request;
    }

    protected function assemble(): void
    {
        $this->add([
            Html::tag('h1', ['class' => 'header'], t('Icinga ServiceNow Setup: choose database resource'))
        ]);

        $form = (new ShowDatabaseSetupForm());
        $form->on($form::ON_SUBMIT, function () use ($form) {
            $database = $form->getValue('db_resource');
            Config::module('servicenow')->setSection('db', ['resource' => $database]);
            Config::module('servicenow')->saveIni();
            Notification::success(t('The database resource was saved successful'));
            $form->handleRequest($this->request);
            Icinga::app()->getResponse()->redirectAndExit(Url::fromPath('servicenow/incidents'));
        });
        $form->handleRequest($this->request);

        $this->add($form);
    }
}
