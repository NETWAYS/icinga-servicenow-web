<?php

namespace Icinga\Module\Servicenow\Forms;

use ipl\Html\FormElement\FieldsetElement;
use ipl\Html\FormElement\InputElement;
use ipl\Html\FormElement\SubmitButtonElement;
use ipl\Html\Html;

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

        $inputWrap = Html::tag('div', ['class' => 'snow-input-wrapper']);

        $fKey = new InputElement('fieldKey', [
            'type'  => 'text',
            'value' => '',
            'placeholder' => 'Key',
        ]);

        $this->registerElement($fKey);
        $inputWrap->addHtml($fKey);

        $fVal = new InputElement('fieldValue', [
            'type'  => 'text',
            'value' => '',
            'placeholder' => 'Value',
        ]);

        $this->registerElement($fVal);
        $inputWrap->add($fVal);

        $this->addHtml($inputWrap);

        if ($this->removeButton !== null) {
            $this->addHtml($this->removeButton);
        }
    }
}
