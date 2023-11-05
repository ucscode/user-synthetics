<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

class UssFormFieldStack extends AbstractUssFormFieldStack
{
    /**
     * @method push
     */
    public function push(UssFormField $field): self
    {
        if(!in_array($field, $this->fieldStack)) {
            $this->fieldStack[] = $field;
            $this->stackContainerElement->appendChild($field->getFieldAsElement());
        }
        return $this;
    }

    public function get(int $index): ?UssFormField
    {
        return $this->fieldStack[$index] ?? null;
    }

    public function getAll(): array
    {
        return $this->fieldStack;
    }

    public function pull(int|UssFormField $indexField): ?UssFormField
    {
        $formField = null;

        if($indexField instanceof UssFormField) {
            $key = array_search($indexField, $this->fieldStack, true);
            if($key !== false) {
                $formField = $indexField;
                unset($this->fieldStack[$key]);
            }
        } else {
            if(array_key_exists($indexField, $this->fieldStack)) {
                $formField = $this->fieldStack[$indexField];
                unset($this->fieldStack[$indexField]);
            }
        };

        if($formField) {
            $field = $formField->getFieldAsElement();
            if($field && $field->hasParentElement()) {
                $field->getParentElement()->removeChild($field);
            }
        };

        $this->fieldStack = array_values($this->fieldStack);

        return $formField;
    }

    /**
     * @method setLegend
     */
    public function getFieldsetElement(): UssElement
    {
        return $this->fieldsetElement;
    }

    /**
     * @method setFieldsetAttribute
     */
    public function setFieldsetAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->fieldsetElement, $name, $value, $append);
    }

    /**
     * @method getFieldsetAttribute
     */
    public function getFieldsetAttribute(string $name): ?string
    {
        return $this->fieldsetElement->getAttribute($name);
    }

    /**
     * @method removeFieldsetAttribute
     */
    public function removeFieldsetAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->fieldsetElement, $name, $detach);
    }

    /**
     * @method getLegendElement
     */
    public function getLegendElement(): UssElement
    {
        return $this->legendElement;
    }

    /**
     * @method setLegendAttribute
     */
    public function setLegendAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->legendElement, $name, $value, $append);
    }

    /**
     * @method getLegendAttribute
     */
    public function getLegendAttribute(string $name): ?string
    {
        return $this->legendElement->getAttribute($name);
    }

    /**
     * @method removeLegendAttribute
     */
    public function removeLegendAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->legendElement, $name, $detach);
    }

    /**
     * @method removeLegendAttribute
     */
    public function setLegendValue(?string $value): self
    {
        $this->legendValue = $value;
        return $this;
    }

    /**
     * @method removeLegendAttribute
     */
    public function getLegendValue(): ?string
    {
        return $this->legendValue;
    }

    /**
     * @method getLegendElement
     */
    public function getStackContainerElement(): UssElement
    {
        return $this->stackContainerElement;
    }

    /**
     * @method setStackContainerAttribute
     */
    public function setStackContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->stackContainerElement, $name, $value, $append);
    }

    /**
     * @method getStackContainerAttribute
     */
    public function getStackContainerAttribute(string $name): ?string
    {
        return $this->stackContainerElement->getAttribute($name);
    }

    /**
     * @method removeStackContainerAttribute
     */
    public function removeStackContainerAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->stackContainerElement, $name, $detach);
    }

    /**
     * @method disableFieldset
     */
    public function setFieldsetDisabled(bool $status): self
    {
        if($status) {
            $this->fieldsetElement->setAttribute('disabled', 'disabled');
        } else {
            $this->fieldsetElement->removeAttribute('disabled');
        }
        return $this;
    }

    /**
     * @method isFieldsetDisabled
     */
    public function isFieldsetDisabled(): bool
    {
        return $this->fieldsetElement->hasAttribute('disabled');
    }

    /**
     * @method getStackAsElement
     */
    public function getStackAsElement(): UssElement
    {
        $this->fieldsetElement->appendChild($this->legendElement);
        $this->fieldsetElement->appendChild($this->stackContainerElement);
        return $this->fieldsetElement;
    }

    /**
     * @method getStackAsHTML
     */
    public function getStackAsHTML(): string
    {
        return $this->getStackAsElement()->getHTML(true);
    }
}