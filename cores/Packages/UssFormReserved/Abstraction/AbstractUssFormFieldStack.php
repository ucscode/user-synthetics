<?php

namespace Ucscode\UssForm\Abstraction;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\Interface\UssFormFieldStackInterface;

abstract class AbstractUssFormFieldStack implements UssFormFieldStackInterface
{
    protected array $outerContainer = [
        'element' => null
    ];

    protected array $title = [
        'element' => null,
        'value' => null,
        'hidden' => false
    ];

    protected array $subtitle = [
        'element' => null,
        'value' => null,
        'hidden' => false
    ];

    protected array $instruction = [
        'element' => null,
        'value' => null,
        'hidden' => false
    ];

    protected array $innerContainer = [
        'element' => null
    ];

    /**
     * @method __construct
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly bool $isFieldset = true,
        public readonly UssForm $relatedForm
    ) {
        $this->buildElements();
    }

    /**
     * @method buildElements
     */
    protected function buildElements(): void
    {
        $elements = [
            'outerContainer' => [
                $this->isFieldset ? UssElement::NODE_FIELDSET : UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'fieldstack-outer-container col-12',
                    'data-fieldstack' => $this->name
                ],
            ],
            'title' => [
                $this->isFieldset ? UssElement::NODE_LEGEND : UssElement::NODE_H2,
                'attributes' => [
                    'class' => 'fieldstack-title'
                ],
            ],
            'subtitle' => [
                UssElement::NODE_P,
                'attributes' => [
                    'class' => 'fieldstack-subtitle small'
                ],
            ],
            'instruction' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'fieldstack-instruction alert alert-info'
                ]
            ],
            'innerContainer' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'row fieldstack-inner-container'
                ],
            ],
        ];

        foreach($elements as $property => $component) {
            $this->{$property}['element'] = new UssElement($component[0]);
            foreach($component['attributes'] as $name => $value) {
                $this->{$property}['element']->setAttribute($name, $value);
            }
        }

        $this->structureElement();
    }

    protected function structureElement(): void
    {
        $this->outerContainer['element']->prependChild($this->title['element']);
        $this->outerContainer['element']->appendChild($this->subtitle['element']);
        $this->outerContainer['element']->appendChild($this->instruction['element']);
        $this->outerContainer['element']->appendChild($this->innerContainer['element']);
    }

    /**
     * @method attributeSetter
     */
    protected function attributeSetter(UssElement $element, string $name, string $value, bool $append): self
    {
        if($append) {
            $element->addAttributeValue($name, $value);
        } else {
            $element->setAttribute($name, $value);
        };
        return $this;
    }

    /**
     * @method setLegend
     */
    protected function attributeRemover(UssElement $element, string $name, ?string $detach): self
    {
        if(is_null($detach)) {
            $element->removeAttribute($name);
        } else {
            $element->removeAttributeValue($name, $detach);
        }
        return $this;
    }

    /**
     * @method valueSetter
     */
    protected function valueSetter(array &$entity, UssElement|string|null $value): self
    {
        $entity['value'] = $value;
        if($value instanceof UssElement) {
            $entity['element']->appendChild($value);
        } else {
            $entity['element']->setContent($value);
        }
        return $this;
    }

    /**
     * @method hideElement
     */
    protected function hideElement(UssElement $element): void
    {
        $parentElement = $element->getParentElement();
        if($parentElement) {
            $parentElement->removeChild($element);
        }
    }
}
