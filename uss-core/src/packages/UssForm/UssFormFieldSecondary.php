<?php

namespace Ucscode\UssForm;

use Ucscode\UssForm\Trait\Field\FieldWidgetContainerTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetTrait;
use Ucscode\UssForm\Trait\UssFormFieldTrait;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Trait\Field\FieldLabelTrait;

class UssFormFieldSecondary
{
    use FieldWidgetContainerTrait;
    use FieldWidgetTrait;
    use FieldLabelTrait;
    use UssFormFieldTrait;

    protected const SECONDARY_TYPES = [
        UssForm::TYPE_HIDDEN,
        UssForm::TYPE_CHECKBOX,
        UssForm::TYPE_RADIO,
        UssForm::TYPE_SWITCH
    ];

    public readonly string $nodeName;
    protected string $prefix = 'secondary-field';

    public function __construct(protected string $nodeType)
    {
        $this->nodeName = UssForm::NODE_INPUT;
        if(!in_array($this->nodeType, self::SECONDARY_TYPES)) {
            $this->nodeType = UssForm::TYPE_HIDDEN;
        }
        $this->generateElements();
        $this->buildFieldStructure();
    }

    public function getFieldAsElement(): UssElement
    {
        return $this->widgetContainer['element'];
    }

    public function getFieldAsHTML(): string
    {
        return $this->getFieldAsElement()->getHTML(true);
    }

    protected function getElementStructure(): array
    {
        return [
            'widgetContainer' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => $this->widgetContainerClass()
                ],
            ],
            'label' => [
                UssElement::NODE_LABEL,
                'attributes' => [
                    'class' => $this->isCheckable() ? 'form-check-label' : 'form-label',
                    'for' => $this->widgetId
                ],
            ]
        ];
    }

    

    protected function buildFieldStructure(): void
    {
        $this->widgetContainer['element']->appendChild($this->widget['element']);

        if($this->isCheckable()) {
            $this->widgetContainer['element']->appendChild($this->label['element']);
        }
    }
}
