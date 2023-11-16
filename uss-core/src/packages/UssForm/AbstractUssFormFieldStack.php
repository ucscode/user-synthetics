<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

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

    protected array $fields = [];
    protected array $elements = [];

    /**
     * @method __construct
     */
    public function __construct(
        public readonly ?string $stackName = null,
        public readonly bool $isFieldset = true
    ) {
        $this->buildElements();
    }

    // /**
    //  * @method setOuterContainerAsDIV
    //  */
    // public function setOuterContainerAsDiv(bool $status): self
    // {
    //     $element = $this->outerContainer['element'];
    //     $node = new UssElement($status ? UssElement::NODE_DIV : UssElement::NODE_FIELDSET);
    //     if($element->tagName !== $node->tagName) {
    //         foreach($element->getAttributes() as $key => $value) {
    //             $node->setAttribute($key, $value);
    //         }
    //         foreach($element->getChildren() as $child) {
    //             $node->appendChild($child);
    //         }
    //         $this->outerContainer['element'] = $node;
    //     }
    //     return $this;
    // }

    // /**
    //  * @method isOuterContainerDIV
    //  */
    // public function isOuterContainerDIV(): bool
    // {
    //     return $this->outerContainer['element']->nodeName === UssElement::NODE_DIV;
    // }

    /**
     * @method buildElements
     */
    protected function buildElements(): void
    {
        $elements = [
            'outerContainer' => [
                $this->isFieldset ? UssElement::NODE_FIELDSET : UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'fieldstack-outer-container',
                    'data-fieldstack' => $this->stackName
                ],
            ],
            'title' => [
                UssElement::NODE_LEGEND,
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
}
