<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

abstract class AbstractUssFormFieldStack implements UssFormFieldStackInterface
{
    protected UssElement $fieldsetElement;
    protected UssElement $legendElement;
    protected UssElement $stackContainerElement;

    protected array $fieldStack = [];
    protected ?string $legendValue;

    /**
     * @method __construct
     */
    public function __construct(
        public readonly ?string $stackName = null
    )
    {
        $this->buildElements();
    }

    /**
     * @method buildElements
     */
    public function buildElements(): void
    {
        $elements = [
            'fieldsetElement' => [
                UssElement::NODE_FIELDSET,
                'attributes' => [
                    'name' => $this->stackName
                ],
            ],
            'legendElement' => [
                UssElement::NODE_LEGEND,
                'attributes' => [

                ],
            ],
            'stackContainerElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'row'
                ],
            ],
        ];

        foreach($elements as $property => $component) {
            $this->{$property} = new UssElement($component[0]);
            foreach($component['attributes'] as $name => $value) {
                $this->{$property}->setAttribute($name, $value);
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
}