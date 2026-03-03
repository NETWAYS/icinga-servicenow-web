<?php

namespace Icinga\Module\Servicenow\Forms;

use Icinga\Module\Servicenow\Client\Snow;

use Icinga\Util\Json;
use Icinga\Web\Session;

use ipl\Html\HtmlElement;
use ipl\Html\Text;
use ipl\Web\Common\CsrfCounterMeasure;
use ipl\Web\Compat\CompatForm;
use ipl\Web\Widget\HorizontalKeyValue;

class IncidentForm extends CompatForm
{
    use CsrfCounterMeasure;

    protected function assemble(): void
    {
        $this->addElement($this->createCsrfCounterMeasure(Session::getSession()->getId()));

        $this->add(new HtmlElement('h2', null, new Text($this->translate('Incident Fields'))));

        $this->add(
            new Text($this->translate(
                'This page enables you to load an ServiceNow incident from the API.'
                    . 'The returned data can be used in custom fields and templates.'
            ))
        );

        $this->addElement(
            'text',
            'incidentsysid',
            [
                'label' => t('Incident SysID'),
                'description' => t('Incident SysID to load.'),
                'required' => true
            ]
        );

        $this->addElement('submit', 'submit', [
            'label' => $this->translate('Load')
        ]);
    }

    protected function onSuccess(): void
    {
        $this->add(new HtmlElement('h2', null, new Text($this->translate('ServiceNow Incident'))));

        $sysid = trim($this->getValue('incidentsysid'));

        $client = Snow::fromConfig();

        $resp = $client->fetch($sysid);

        try {
            $data = $resp->getBody()->getContents();
        } catch (Exception $e) {
            $msg = new Text($this->translate('Could not fetch incident'));
            $this->add(HtmlElement::create('span', ['class' => 'errors'], $msg));
            $this->add(new Text($e->getMessage()));
            return;
        }

        $fields = [];

        try {
            $fields = Json::decode($data, true);
        } catch (JsonDecodeException $e) {
            $msg = new Text($this->translate('Could not decode incident data'));
            $this->add(HtmlElement::create('span', ['class' => 'errors'], $msg));
            return;
        }

        if (!array_key_exists('result', $fields)) {
            $msg = new Text($this->translate('Invalid data received'));
            $this->add(HtmlElement::create('span', ['class' => 'errors'], $msg));
            return;
        }

        if (count($fields['result']) < 1) {
            $msg = new Text($this->translate('No such incident'));
            $this->add(HtmlElement::create('span', ['class' => 'errors'], $msg));
            return;
        }

        $d = $fields['result'][0];

        foreach ($d as $key => $val) {
            $info = new HorizontalKeyValue($key, $val);
            $this->add($info);
        }
    }
}
