<?php

namespace Icinga\Module\Servicenow\Forms;

use Icinga\Util\Json;
use Icinga\Web\Session;

use ipl\Html\Html;
use ipl\I18n\Translation;
use ipl\Sql\Connection;
use ipl\Web\Common\CsrfCounterMeasure;
use ipl\Web\Compat\CompatForm;
use ipl\Web\Widget\EmptyStateBar;
use ipl\Web\Widget\Icon;
use ipl\Html\FormElement\SubmitButtonElement;

class TemplateForm extends CompatForm
{
    use Translation;
    use CsrfCounterMeasure;

    protected $db = null;
    protected $template = null;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function hasBeenSaved()
    {
        $btn = $this->getPressedSubmitElement();
        $csrf = $this->getElement('CSRFToken');

        return $csrf !== null && $csrf->isValid() && $btn !== null && $btn->getName() === 'save';
    }

    public function hasBeenRemoved(): bool
    {
        $btn = $this->getPressedSubmitElement();
        $csrf = $this->getElement('CSRFToken');

        return $csrf !== null && $csrf->isValid() && $btn !== null && $btn->getName() === 'remove';
    }

    protected function createRemoveButton(int $no): SubmitButtonElement
    {
        $remove = $this->createElement('submitButton', sprintf('remove_%d', $no), [
            'formnovalidate' => true,
            'title' => $this->translate('Remove this field from template'),
            'label' => new Icon('x'),
        ]);

        $this->registerElement($remove);

        return $remove;
    }

    protected function createAddButton(): SubmitButtonElement
    {
        $add = $this->createElement('submitButton', 'add-field', [
            'formnovalidate' => true,
            'label' => $this->translate('Add Field'),
            'title' => $this->translate('Add new field to template'),
        ]);

        return $add;
    }

    public function load($template): void
    {
        $this->template = $template;

        $fieldCount = count($template->getFields());
        $fields = $template->getFields();

        $populate = [
            'display_name' => $template->display_name,
            'count' => $fieldCount,
        ];

        $keys = array_keys($this->template->getFields());
        $vals = array_values($this->template->getFields());

        for ($i = 0; $i < $fieldCount; $i++) {
            $_k = $keys[$i] ?? '';
            $_v = $vals[$i] ?? '';
            $populate[sprintf('field_%d', $i)] = TemplateField::prepare(['id' => $i, 'fieldKey' => $_k, 'fieldValue' => $_v]);
        }

        $this->populate($populate);
    }

    protected function assemble()
    {
        $this->addElement($this->createCsrfCounterMeasure(Session::getSession()->getId()));

        $this->addElement('text', 'display_name', [
            'required' => true,
            'label' => $this->translate('Display Name'),
        ]);

        $this->add(Html::tag('h2', 'Fields'));

        $expectedCount = (int) $this->getPopulatedValue('count', 0); // Want we get from poplulate or it's 0 (new template)
        $count = 0; // Increases until $expectedCount is reached, ensuring proper association with form data
        $actualCount = 0; // The actual number of restored elements, minus the one that has been removed

        while ($count < $expectedCount) {
            $remove = $this->createRemoveButton($count);
            if ($remove->hasBeenPressed()) {
                $this->clearPopulatedValue($remove->getName());
                $this->clearPopulatedValue($count);

                // Re-index populated values to ensure proper association with form data
                foreach (range($count + 1, $expectedCount) as $i) {
                    $expectedValue = $this->getPopulatedValue(sprintf('field_%d', $i));
                    if ($expectedValue !== null) {
                        $this->populate([sprintf('field_%d', $i - 1) => $expectedValue]);
                    }
                }
            } else {
                $actualCount++;
            }
            $count++;
        }

        $addBtn = $this->createAddButton();
        $this->registerElement($addBtn);
        $this->decorate($addBtn);
        if ($addBtn->hasBeenPressed()) {
            $this->createRemoveButton($actualCount);
            $actualCount++;
        }

        for ($i = 0; $i < $actualCount; $i++) {
            $remove = $this->getElement(sprintf('remove_%d', $i));
            $element = new TemplateField(sprintf('field_%d', $i));
            $element->setRemoveButton($remove);
            $this->addElement($element);
        }

        if ($actualCount === 0) {
            $this->addHtml(new EmptyStateBar($this->translate('No fields configured')));
        }

        $this->clearPopulatedValue('count');
        $this->addElement('hidden', 'count', ['ignore' => true, 'value' => $actualCount]);

        $btns = Html::tag('div', ['class' => 'control-group form-controls']);

        $removeBtn = $this->createElement('submit', 'remove', [
            'title' => $this->translate('Remove Template'),
            'label' => $this->translate('Remove Template'),
            'data-confirmation' => $this->translate('Confirm'),
            'class' => ['btn-remove', 'confirm-button']
        ]);
        $this->registerElement($removeBtn);
        $this->decorate($removeBtn);
        $btns->add($removeBtn);

        $btns->add($addBtn);

        $saveBtn = $this->createElement('submit', 'save', [
            'title' => $this->translate('Save Template'),
            'label' => $this->translate('Save Template'),
        ]);
        $this->registerElement($saveBtn);
        $this->decorate($saveBtn);
        $btns->add($saveBtn);

        $this->add($btns);
    }

    public function removeTemplate(): void
    {
        if ($this->template === null) {
            return;
        }

        $this->db->delete('template', [ 'id = ?' => $this->template->id ]);
    }

    public function upsertTemplate(): void
    {
        $displayName = $this->getValue('display_name');

        $f = $this->getFieldElements();
        $fields = Json::sanitize($f);

        if ($this->template === null) {
            $this->db->insert('template', [
                'display_name' => $displayName,
                'fields' => $fields,
            ]);

            return;
        }

        $this->db->update('template', [
            'fields' => $fields
        ], ['id = ?' => $this->template->id]);
    }

    public function getFieldElements(): array
    {
        $fields = [];
        foreach ($this->ensureAssembled()->getElements() as $element) {
            if ($element instanceof TemplateField) {
                $fields = array_merge($fields, $element->getValues());
            }
        }

        return $fields;
    }
}
