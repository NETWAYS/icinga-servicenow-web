<?php

namespace Icinga\Module\Servicenow\Widget;

use Icinga\Date\DateFormatter;

use ipl\Html\FormattedString;
use ipl\I18n\Translation;
use ipl\Web\Widget\StateBall;
use ipl\Web\Url;
use ipl\Web\Widget\Link;
use ipl\Html\Attributes;
use ipl\Html\HtmlDocument;
use ipl\Html\Text;
use ipl\Web\Common\ItemRenderer;

class IncidentRenderer implements ItemRenderer
{
    use Translation;

    public function assembleAttributes($item, Attributes $attributes, string $layout): void
    {
        $attributes->get('class')->addValue('snow-incident');
    }

    public function assembleVisual($item, HtmlDocument $visual, string $layout): void
    {
        $st = strtolower($item->notification_state);
        $tp = strtolower($item->notification_type);
        $stateBall = new StateBall($st, StateBall::SIZE_BIG);

        switch ($tp) {
            case 'acknowledgement':
                $stateBall->getAttributes()->add('class', 'handled');
                break;
            case 'recovery':
                $stateBall->getAttributes()->add('class', 'handled');
                break;
            case 'flappingend':
                $stateBall->getAttributes()->add('class', 'handled');
                break;
            case 'downtimeend':
                $stateBall->getAttributes()->add('class', 'handled');
                break;
        }

        $visual->addHtml($stateBall);
    }

    public function assembleTitle($item, HtmlDocument $title, string $layout): void
    {
        $l = new Link($item->incident_number, Url::fromPath('servicenow/incident', ['id' => $item->id]), ['class' => 'subject']);

        $title->addHtml($l);

        $t = new Text('Incident for ' . $item->service_name . ' on ' . $item->host_name);

        if ($item->service_name == "") {
            $t = new Text('Incident on ' . $item->host_name);
        }

        $title->addHtml($t);
    }

    public function assembleCaption($item, HtmlDocument $caption, string $layout): void
    {
        $state = new Text('State: ' . $item->notification_state . ' ');
        $type = new Text('Type: ' . $item->notification_type);

        $caption->addHtml($state);
        $caption->addHtml($type);
    }

    public function assembleExtendedInfo($item, HtmlDocument $info, string $layout): void
    {
        $info->addHtml(
            FormattedString::create(
                $this->translate("created %s"),
                DateFormatter::timeSince(strtotime($item->db_created_at), true)
            )
        );
    }

    public function assembleFooter($item, HtmlDocument $footer, string $layout): void
    {
    }

    public function assemble($item, string $name, HtmlDocument $element, string $layout): bool
    {
        return false;
    }
}
