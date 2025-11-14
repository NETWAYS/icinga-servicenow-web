<?php

namespace Icinga\Module\Servicenow\Forms;

use Icinga\Module\Servicenow\Model\Template;

use Icinga\Web\Session;

use ipl\Html\Html;
use ipl\Sql\Connection;
use ipl\Web\Compat\CompatForm;
use ipl\Web\Common\CsrfCounterMeasure;
use ipl\I18n\Translation;

class TemplateForm extends CompatForm
{
    use Translation;
    use CsrfCounterMeasure;

    protected $db = null;
    protected $template = null;

    public function __construct(Connection $db, ?Template $template = null)
    {
        $this->db = $db;
        $this->template = $template;
    }

    protected function assemble()
    {
        $this->addElement($this->createCsrfCounterMeasure(Session::getSession()->getId()));

        $this->addElement('text', 'display_name', [
            'required' => true,
            'label' => 'Display Name',
        ]);

        $this->add(Html::tag('h2', 'Fields'));

        // TODO: Hacky, needs to be a fieldset with key/values
        $this->addElement('textarea', 'template_json', [
            'label' => 'Template',
            'class' => 'template-editor',
            'required' => true,
        ]);

        $this->addElement('submit', 'remove', [
            'label' => $this->translate('Remove')
        ]);

        $this->addElement('submit', 'save', [
            'label' => $this->translate('Save')
        ]);
    }

    public function hasBeenRemoved(): bool
    {
        $btn = $this->getPressedSubmitElement();
        $csrf = $this->getElement('CSRFToken');

        return $csrf !== null && $csrf->isValid() && $btn !== null && $btn->getName() === 'remove';
    }

    public function hasBeenSaved(): bool
    {
        $btn = $this->getPressedSubmitElement();
        $csrf = $this->getElement('CSRFToken');

        return $csrf !== null && $csrf->isValid() && $btn !== null && $btn->getName() === 'save';
    }

    public function removeTemplate(): void
    {
        if ($this->template === null) {
            return;
        }

        $this->db->delete(
            'template',
            [
                'id = ?' => $this->template->id,
            ]
        );
    }

    public function upsertTemplate(): void
    {
        if ($this->template === null) {
            $this->db->insert('template', [
                'display_name' => $this->getValue('display_name'),
                'fields' => $this->getValue('template_json')
            ]);

            return;
        }

        $this->db->update('template', [
            'fields' => $this->getValue('template_json')
        ], ['id = ?' => $this->template->id]);
    }
}
