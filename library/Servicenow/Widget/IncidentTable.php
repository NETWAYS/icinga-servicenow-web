<?php

namespace Icinga\Module\Servicenow\Widget;

use Icinga\Date\DateFormatter;

use ipl\Html\Table;
use ipl\Html\Html;
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
            $this->translate('Incident'),
            $this->translate('Summary'),
            $this->translate('Created'),
        ], null, 'th'));

        $tbody = $this->getBody();

        foreach ($this->incidents as $incident) {
            $created = Html::tag(
                'span',
                ['title' => $incident->db_created_at, 'class' => 'time-since'],
                DateFormatter::timeSince(strtotime($incident->db_created_at), true)
            );

            $r = Table::row([
                $incident->incident_number,
                $incident->notification_output,
                $created,
            ]);

            $tbody->addHtml($r);
        }
    }
}
