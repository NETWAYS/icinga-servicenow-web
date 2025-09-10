<?php

namespace Icinga\Module\Servicenow\Widget;

use ipl\Html\Table;
use ipl\I18n\Translation;

class IncidentTable extends Table
{
    use Translation;

    protected $defaultAttributes = ['class' => 'common-table'];

    protected $incidents;

    public function __construct($incidents)
    {
        $this->incidents = $incidents;
    }

    protected function assemble()
    {
        $this->getHeader()->addHtml(self::row([
            $this->translate('Number'),
            $this->translate('Description'),
        ], null, 'th'));

        $tbody = $this->getBody();

        foreach ($this->incidents as $incident) {
            $r = Table::row([
                $incident->incident_number,
                $incident->notification_output,
            ]);

            $tbody->addHtml($r);
        }
    }
}
