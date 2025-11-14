<?php

namespace Icinga\Module\Servicenow\Widget;

use ipl\Html\Table;
use ipl\Html\Html;
use ipl\Web\Url;
use ipl\Web\Widget\Link;
use ipl\I18n\Translation;

class TemplateTable extends Table
{
    use Translation;

    protected $defaultAttributes = [
        'class' => 'common-table table-row-selectable template-table',
        'data-base-target' => '_next',
    ];

    protected $templates;

    public function __construct($templates)
    {
        $this->templates = $templates;
    }

    protected function assemble()
    {
        $this->getHeader()->addHtml(self::row([
            $this->translate('Name'),
            $this->translate('Fields'),
        ], null, 'th'));

        $tbody = $this->getBody();

        foreach ($this->templates as $template) {
            $title = Html::tag('span')->add(
                new Link($template->display_name, Url::fromPath('servicenow/template', ['id' => $template->id]))
            );

            $fields = join(', ', array_keys($template->getFields()));

            $r = Table::row([
                $title,
                $fields,
            ]);

            $tbody->addHtml($r);
        }
    }
}
