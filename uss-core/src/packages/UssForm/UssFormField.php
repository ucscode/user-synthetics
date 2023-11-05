<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

class UssFormField extends AbstractUssFormField
{
    /**
     * For: Row
     */
    public function getRowElement(): UssElement
    {
        return $this->rowElement;
    }

    public function setRowAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->rowElement, $name, $value, $append);
    }

    public function getRowAttribute(string $name): ?string
    {
        return $this->rowElement->getAttribute($name);
    }

    public function removeRowAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->rowElement, $name, $detach);
    }

    /**
     * For: Container
     */
    public function getContainerElement(): UssElement
    {
        return $this->containerElement;
    }

    public function setContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->containerElement, $name, $value, $append);
    }

    public function getContainerAttribute(string $name): ?string
    {
        return $this->containerElement->getAttribute($name);
    }

    public function removeContainerAttribute(string $name, ?string $detach = null): self
    {
        $this->attributeRemover($this->containerElement, $name, $detach);
        return $this;
    }

    /**
     * For: Info
     */
    public function getInfoElement(): UssElement
    {
        return $this->infoElement;
    }

    public function setInfoAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->infoElement, $name, $value, $append);
    }

    public function getInfoAttribute(string $name): ?string
    {
        return $this->infoElement->getAttribute($name);
    }

    public function removeInfoAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->infoElement, $name, $detach);
    }

    public function setInfoMessage(null|string|UssElement $value, ?string $icon = null): self
    {
        $this->infoValue = $value;
        $this->infoIcon = null;
        return $this;
    }

    public function getInfoMessage(): null|string|UssElement
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

    public function setLabelAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->labelElement, $name, $value, $append);
    }

    public function getLabelAttribute(string $name): ?string
    {
        return $this->labelElement->getAttribute($name);
    }

    public function removeLabelAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->labelElement, $name, $detach);
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
    public function getValidationElement(): UssElement
    {
        return $this->validationElement;
    }

    public function setValidationAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->validationElement, $name, $value, $append);
    }

    public function getValidationAttribute(string $name): ?string
    {
        return $this->validationElement->getAttribute($name);
    }

    public function removeValidationAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->validationElement, $name, $detach);
    }

    public function setValidationType(?string $validationType): self
    {
        $this->validationType = $validationType;
        return $this;
    }

    public function getValidationType(): ?string
    {
        return $this->validationType;
    }

    public function setValidationMessage(?string $value, ?string $icon = null): self
    {
        $this->validationValue = $value;
        $this->validationIcon = $icon;
        return $this;
    }

    public function getValidationMessage(): ?string
    {
        return $this->validationValue;
    }

    /**
     * For: WidgetContainer
     */
    public function getWidgetContainerElement(): UssElement
    {
        return $this->widgetContainerElement;
    }

    public function setWidgetContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->widgetContainerElement, $name, $value, $append);
    }

    public function getWidgetContainerAttribute(string $name): ?string
    {
        return $this->widgetContainerElement->getAttribute($name);
    }

    public function removeWidgetContainerAttribute(string $name, ?string $detach): self
    {
        $method = !is_null($detach) ? 'removeAttributeValue' : 'removeAttribute';
        $this->widgetContainerElement->{$method}($name, $detach);
        return $this;
    }

    /**
     * For: Widget
     */
    public function getWidgetElement(): UssElement
    {
        return $this->widgetElement;
    }

    public function setWidgetAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->widgetElement, $name, $value, $append);
    }

    public function getWidgetAttribute(string $name): ?string
    {
        return $this->widgetElement->getAttribute($name);
    }

    public function removeWidgetAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->widgetElement, $name, $detach);
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

    public function appendToWidget(null|string|UssElement $appendant): self
    {
        $this->widgetAppendant = $this->refactorInputGroupContent($appendant);
        return $this;
    }

    public function getWidgetAppendant(): ?UssElement
    {
        return $this->widgetAppendant;
    }

    public function prependToWidget(null|string|UssElement $prependant): self
    {
        $this->widgetPrependant = $this->refactorInputGroupContent($prependant);
        return $this;
    }

    public function getWidgetPrependant(): ?UssElement
    {
        return $this->widgetPrependant;
    }

    /**
     * For: Widget Modifier
     */
    public function setWidgetOptions(array $options): self
    {
        $this->widgetOptions['values'] = $options;
        $this->rebuildWidgetOptionsElements($options);
        return $this;
    }

    public function setWidgetOption(string $key, string $displayValue): self
    {
        $optionElement = $this->widgetOptions['elements'][$key] ?? null;
        if(!$optionElement) {
            $optionElement = $this->createOptionElement($key, $displayValue);
            $this->widgetElement->appendChild($optionElement);
        } else {
            $optionElement->setContent($displayValue);
        }
        $this->widgetOptions['values'][$key] = $displayValue;
        $this->widgetOptions['elements'][$key] = $optionElement;
        return $this;
    }

    public function removeWidgetOption(string $key): self
    {
        if($this->hasWidgetOption($key)) {
            $optionElement = $this->getWidgetOptionElement($key);
            unset($this->widgetOptions['values'][$key]);
            unset($this->widgetOptions['elements'][$key]);
            $optionElement->getParentElement()->removeChild($optionElement);
        }
        return $this;
    }

    public function getWidgetOptions(): array
    {
        return $this->widgetOptions;
    }

    public function hasWidgetOption(string $key): bool
    {
        return array_key_exists($key, $this->widgetOptions['values']);
    }
    
    public function getWidgetOptionElement(string $key): ?UssElement
    {
        return $this->widgetOptions['elements'][$key] ?? null;
    }

    /**
     * @method otherWidgetMethods
     */
    public function setWidgetChecked(bool $status): self
    {
        if($this->isCheckable()) {
            if($status) {
                $this->widgetElement->setAttribute('checked', 'checked');
            } else {
                $this->widgetElement->removeAttribute('checked');
            }
        }
        return $this;
    }

    public function isWidgetChecked(): bool
    {
        return $this->isCheckable() && $this->widgetElement->hasAttribute('checked');
    }

    public function setWidgetDisabled(bool $status): self
    {
        if($status) {
            $this->widgetElement->setAttribute('disabled', 'disabled');
        } else {
            $this->widgetElement->removeAttribute('disabled');
        }
        return $this;
    }

    public function isWidgetDisabled(): bool
    {
        return $this->widgetElement->hasAttribute('disabled');
    }

    public function setWidgetReadonly(bool $status): self
    {
        if($status) {
            $this->widgetElement->setAttribute('readonly', 'readonly');
        } else {
            $this->widgetElement->removeAttribute('readonly');
        }
        return $this;
    }

    public function isWidgetReadonly(): bool
    {
        return $this->widgetElement->hasAttribute('readonly');
    }

    /**
     * @method getFieldAsElement
     */
    public function getFieldAsElement(): UssElement
    {
        $this->rowElement->appendChild($this->containerElement);

        if($this->isCheckable()) {
            $this->containerElement->appendChild($this->infoElement);
        } elseif(!$this->isButton()) {
            $this->containerElement->appendChild($this->labelElement);
            $this->containerElement->appendChild($this->infoElement);
        }

        $this->containerElement->appendChild($this->widgetContainerElement);
        $this->widgetContainerElement->appendChild($this->widgetElement);

        if(!$this->isButton()) {

            if(!$this->isCheckable()) {

                if(!empty($this->widgetAppendant) || !empty($this->widgetPrependant)) {

                    $this->widgetContainerElement->addAttributeValue('class', 'input-group');

                    if($this->widgetPrependant) {
                        $this->widgetContainerElement->insertBefore(
                            $this->widgetPrependant,
                            $this->widgetElement
                        );
                    }

                    if($this->widgetAppendant) {
                        $this->widgetContainerElement->insertAfter(
                            $this->widgetAppendant,
                            $this->widgetElement
                        );
                    }

                }

            } else {
                $this->widgetContainerElement->appendChild($this->labelElement);
            }

            $this->containerElement->appendChild($this->validationElement);

            if($this->validationType) {

                if($this->validationType === self::VALIDATION_SUCCESS) {
                    $validation = 'text-success';
                    if(!$this->validationIcon) {
                        $this->validationIcon = 'bi bi-check-circle';
                    }
                } else {
                    $validation = 'text-danger';
                    if(!$this->validationIcon) {
                        $this->validationIcon = 'bi bi-exclamation-circle';
                    }
                }

                $this->validationElement->addAttributeValue(
                    'class',
                    $validation . ' is-' . $this->validationType
                );

            }

            $this->insertElementValue('info', $this->infoIcon ?? 'bi bi-info-circle');
            $this->insertElementValue('label');
            $this->insertElementValue('validation', $this->validationIcon);

        }

        return $this->rowElement;
    }

    /**
     * @method getFieldAsHTML
     */
    public function getFieldAsHTML(): string
    {
        return $this->getFieldAsElement()->getHTML(true);
    }
}
