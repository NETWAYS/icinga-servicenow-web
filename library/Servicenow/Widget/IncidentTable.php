<?php

namespace Icinga\Module\Servicenow\Widget;

use Icinga\Date\DateFormatter;

use ipl\Html\Table;
use ipl\Html\Html;
use ipl\Web\Url;
use ipl\Web\Widget\Link;
use ipl\I18n\Translation;

class IncidentTable extends Table
{
    use Translation;

    protected $defaultAttributes = [
        'class' => 'common-table table-row-selectable incident-table',
        'data-base-target' => '_next',
    ];

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

            $title = Html::tag('span')->add(
                new Link($incident->incident_number, Url::fromPath('servicenow/incident', ['id' => $incident->id]))
            );

            $r = Table::row([
                $title,
                $incident->notification_output,
                $created,
            ]);

            $tbody->addHtml($r);
        }
    }
}
