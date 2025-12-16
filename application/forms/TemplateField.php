<?php

namespace Icinga\Module\Servicenow\Forms;

use ipl\Html\FormElement\FieldsetElement;
use ipl\Html\FormElement\SubmitButtonElement;

class TemplateField extends FieldsetElement
{
    protected $defaultAttributes = ['class' => 'snow-template-field'];
    protected SubmitButtonElement $removeButton;

    public function __construct(string $name, $attributes = null)
    {
        parent::__construct($name, $attributes);
    }

    public function setRemoveButton(SubmitButtonElement $removeButton): void
    {
        $this->removeButton = $removeButton;
    }

    public static function prepare(array $field): array
    {
        return [
            'id' => $field['id'] ?? '',
            'fieldKey' => $field['fieldKey'] ?? '',
            'fieldValue' => $field['fieldValue'] ?? '',
        ];
    }

    public function getValues(): array
    {
         return [ $this->getElement('fieldKey')->getValue() => $this->getElement('fieldValue')->getValue() ];
    }

    protected function assemble(): void
    {
        $this->addElement('hidden', 'id');

        $this->addElement('text', 'fieldKey', [
            'value' => '',
            'label' => 'Key',
        ]);

        $this->addElement('text', 'fieldValue', [
            'value' => '',
            'label' => 'Value',
        ]);

        if ($this->removeButton !== null) {
            $this->addHtml($this->removeButton);
        }
    }
}
