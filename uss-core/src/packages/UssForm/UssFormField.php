<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use UssFormFieldInterface;

class UssFormField implements UssFormFieldInterface
{
    /**
     * Containers: No values but has attributes and holds elements 
     */
    protected UssElement $rowElement;
    protected UssElement $containerElement;
    protected UssElement $widgetContainerElement;

    /**
     * Elements: Has attributes and contain values
     */
    protected UssElement $infoElement;
    protected UssElement $labelElement;
    protected UssElement $errorElement;
    protected UssElement $widgetElement;

    /**
     * $values: The values for each elements
     */
    protected null|string|UssElement $infoValue;
    protected null|string|UssElement $labelValue;
    protected null|string|UssElement $errorValue;
    protected ?string $widgetValue;

    /**
     * Widget Group: append or prepend gadget (icon, button etc) to widgets
     */
    protected null|string|UssElement $widgetAppendant;
    protected null|string|UssElement $widgetPrependant;

    /**
     * Other Widget Resource
     */
    protected array $widgetOptions = [];

    /**
     * @method __constuct
     */
    public function __construct(
        public readonly string $fieldName, 
        public readonly string $nodeName,
        public readonly ?string $nodeType = null
    ){
        $this->generateElements();
    }

    /**
     * For: Row
     */
    public function getRowElement(): UssElement
    {
        return $this->rowElement;
    }

    public function setRowAttribute(string $name, string $value): self
    {
        $this->rowElement->setAttribute($name, $value);
        return $this;
    }

    public function getRowAttribute(string $name): ?string
    {
        return $this->rowElement->getAttribute($name);
    }

    public function removeRowAttribute(string $name): self
    {
        $this->rowElement->removeAttribute($name);
        return $this;
    }

    /**
     * For: Container
     */
    public function getContainerElement(): UssElement
    {
        return $this->containerElement;
    }

    public function setContainerAttribute(string $name, string $value): self
    {
        $this->containerElement->setAttribute($name, $value);
        return $this;
    }

    public function getContainerAttribute(string $name): ?string
    {
        return $this->containerElement->getAttribute($name);
    }

    public function removeContainerAttribute(string $name): self
    {
        $this->containerElement->removeAttribute($name);
        return $this;
    }

    /**
     * For: Info
     */
    public function getInfoElement(): UssElement
    {
        return $this->infoElement;
    }

    public function setInfoAttribute(string $name, string $value): self
    {
        $this->infoElement->setAttribute($name, $value);
        return $this;
    }
    
    public function getInfoAttribute(string $name): ?string
    {
        return $this->infoElement->getAttribute($name);
    }

    public function removeInfoAttribute(string $name): self
    {
        $this->infoElement->removeAttribute($name);
        return $this;
    }

    public function setInfoValue(null|string|UssElement $value): self
    {
        $this->infoValue = $value;
        return $this;
    }

    public function getInfoValue(): null|string|UssElement
    {
        return $this->infoValue;
    }
    
    /**
     * For: Label
     */
    public function getLabelElement(): UssElement
    {
        return $this->labelElement;
    }

    public function setLabelAttribute(string $name, string $value): self
    {
        $this->labelElement->setAttribute($name, $value);
        return $this;
    }

    public function getLabelAttribute(string $name): ?string
    {
        return $this->labelElement->getAttribute($name);
    }

    public function removeLabelAttribute(string $name): self
    {
        $this->labelElement->removeAttribute($name);
        return $this;
    }

    public function setLabelValue(null|string|UssElement $value): self
    {
        $this->labelValue = $value;
        return $this;
    }

    public function getLabelValue(): null|string|UssElement
    {
        return $this->labelValue;
    }

    /**
     * For: Error
     */
    public function getErrorElement(): UssElement
    {
        return $this->errorElement;
    }

    public function setErrorAttribute(string $name, string $value): self
    {
        $this->errorElement->setAttribute($name, $value);
        return $this;
    }

    public function getErrorAttribute(string $name): ?string
    {
        return $this->errorElement->getAttribute($name);
    }

    public function removeErrorAttribute(string $name): self
    {
        $this->errorElement->removeAttribute($name);
        return $this;
    }

    public function setErrorValue(null|string|UssElement $value): self
    {
        $this->errorValue = $value;
        return $this;
    }

    public function getErrorValue(): null|string|UssElement
    {
        return $this->errorValue;
    }

    /**
     * For: WidgetContainer
     */
    public function getWidgetContainerElement(): UssElement
    {
        return $this->widgetContainerElement;
    }

    public function setWidgetContainerAttribute(string $name, string $value): self
    {
        $this->widgetContainerElement->setAttribute($name, $value);
        return $this;
    }

    public function getWidgetContainerAttribute(string $name): ?string
    {
        return $this->widgetContainerElement->getAttribute($name);
    }

    public function removeWidgetContainerAttribute(string $name): self
    {
        $this->widgetContainerElement->removeAttribute($name);
        return $this;
    }

    /**
     * For: Widget
     */
    public function getWidgetElement(): UssElement
    {
        return $this->widgetElement;
    }

    public function setWidgetAttribute(string $name, string $value): self
    {
        $this->widgetElement->setAttribute($name, $value);
        return $this;
    }

    public function getWidgetAttribute(string $name): ?string
    {
        return $this->widgetElement->getAttribute($name);
    }

    public function removeWidgetAttribute(string $name): self
    {
        $this->widgetElement->removeAttribute($name);
        return $this;
    }

    public function setWidgetValue(?string $value): self
    {
        $this->widgetValue = $value;
        return $this;
    }

    public function getWidgetValue(): ?string
    {
        return $this->widgetValue;
    }

    public function setWidgetAppendant(null|string|UssElement $appendant): self
    {
        $this->widgetAppendant = $appendant;
        return $this;
    }

    public function getWidgetAppendant(): null|string|UssElement
    {
        return $this->widgetAppendant;
    }

    public function setWidgetPrependant(null|string|UssElement $prependant): self
    {
        $this->widgetPrependant = $prependant;
        return $this;
    }

    public function getWidgetPrependant(): null|string|UssElement
    {
        return $this->widgetPrependant;
    }

    /**
     * For: Widget Modifier
     */
    public function setWidgetOptions(array $options): self
    {
        $this->widgetOptions = $options;
        return $this;
    }

    public function setWidgetOption(string $key, string $displayValue): self
    {
        $this->widgetOptions[$key] = $displayValue;
        return $this;
    }

    public function removeWidgetOption(string $key): self
    {
        if($this->hasWidgetOption($key)) {
            unset($this->widgetOptions[$key]);
        }
        return $this;
    }

    public function getWidgetOptions(): array
    {
        return $this->widgetOptions;
    }

    public function hasWidgetOption(string $key): bool
    {
        return array_key_exists($key, $this->widgetOptions);
    }

    /**
     * Protected Methods
     */
    protected function generateElements(): void
    {
        $elements = [
            'rowElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'col-12',
                    'data-field' => $this->fieldName
                ],
            ],
            'containerElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'field-container',
                ],
            ],
            'widgetContainerElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-single form-checker'
                ],
            ],
            'infoElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-info'
                ],
            ],
            'labelElement' => [
                UssElement::NODE_LABEL,
                'attributes' => [
                    'class' => 'form-label'
                ],
            ],
            'errorElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-error'
                ],
            ],
        ];

        foreach($elements as $name => $prop) {
            $this->{$name} = new UssElement($prop[0]);
            foreach($prop['attributes'] as $key => $value) {
                $this->{$name}->setAttribute($key, $value);
            }
        }

        $this->widgetElement;
    }
}
