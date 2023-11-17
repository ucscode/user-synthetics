<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Abstraction\AbstractUssFormField;

class UssFormField extends AbstractUssFormField
{
    /**
     * For: Row
     */
    public function getRowElement(): UssElement
    {
        return $this->row['element'];
    }

    public function setRowAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->row['element'], $name, $value, $append);
    }

    public function getRowAttribute(string $name): ?string
    {
        return $this->row['element']->getAttribute($name);
    }

    public function removeRowAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->row['element'], $name, $detach);
    }

    /**
     * For: Container
     */
    public function getContainerElement(): UssElement
    {
        return $this->container['element'];
    }

    public function setContainerAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->container['element'], $name, $value, $append);
    }

    public function getContainerAttribute(string $name): ?string
    {
        return $this->container['element']->getAttribute($name);
    }

    public function removeContainerAttribute(string $name, ?string $detach = null): self
    {
        $this->attributeRemover($this->container['element'], $name, $detach);
        return $this;
    }

    /**
     * For: Info
     */
    public function getInfoElement(): UssElement
    {
        return $this->info['element'];
    }

    public function setInfoAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->info['element'], $name, $value, $append);
    }

    public function getInfoAttribute(string $name): ?string
    {
        return $this->info['element']->getAttribute($name);
    }

    public function removeInfoAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->info['element'], $name, $detach);
    }

    public function setInfoMessage(null|string|UssElement $value, ?string $icon = null): self
    {
        $this->info['value'] = $value;
        $this->insertElementValue(
            $this->info['element'],
            $value,
            func_num_args() === 2 ? $icon : 'bi bi-info-circle'
        );
        return $this;
    }

    public function getInfoMessage(): null|string|UssElement
    {
        return $this->info['value'];
    }

    /**
     * For: Label
     */
    public function getLabelElement(): UssElement
    {
        return $this->label['element'];
    }

    public function setLabelAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->label['element'], $name, $value, $append);
    }

    public function getLabelAttribute(string $name): ?string
    {
        return $this->label['element']->getAttribute($name);
    }

    public function removeLabelAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->label['element'], $name, $detach);
    }

    public function setLabelValue(null|string|UssElement $value): self
    {
        $this->label['value'] = $value;
        if($value instanceof UssElement) {
            $this->label['element']->appendChild($value);
        } else {
            $this->label['element']->setContent($value);
        }
        return $this;
    }

    public function getLabelValue(): null|string|UssElement
    {
        return $this->label['value'];
    }

    /**
     * For: Error
     */
    public function getValidationElement(): UssElement
    {
        return $this->validation['element'];
    }

    public function setValidationAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->validation['element'], $name, $value, $append);
    }

    public function getValidationAttribute(string $name): ?string
    {
        return $this->validation['element']->getAttribute($name);
    }

    public function removeValidationAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->validation['element'], $name, $detach);
    }

    public function setValidationType(?string $validationType): self
    {
        $this->validation['type'] = $validationType;
        $this->validationExec();
        return $this;
    }

    public function getValidationType(): ?string
    {
        return $this->validation['type'];
    }

    public function setValidationMessage(?string $value): self
    {
        $this->validation['value'] = $value;
        $this->validationExec();
        return $this;
    }

    public function getValidationMessage(): ?string
    {
        return $this->validation['value'];
    }

    /**
     * For: WidgetContainer
     */
    public function getWidgetContainerElement(): UssElement
    {
        return $this->widgetContainer['element'];
    }

    public function setWidgetContainerAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->widgetContainer['element'], $name, $value, $append);
    }

    public function getWidgetContainerAttribute(string $name): ?string
    {
        return $this->widgetContainer['element']->getAttribute($name);
    }

    public function removeWidgetContainerAttribute(string $name, ?string $detach): self
    {
        $method = !is_null($detach) ? 'removeAttributeValue' : 'removeAttribute';
        $this->widgetContainer['element']->{$method}($name, $detach);
        return $this;
    }

    /**
     * For: Widget
     */
    public function getWidgetElement(): UssElement
    {
        return $this->widget['element'];
    }

    public function setWidgetAttribute(string $name, ?string $value, bool $append = false): self
    {
        if(strtolower(trim($name)) === 'value') {
            switch($this->widget['element']->nodeName) {
                case UssElement::NODE_INPUT:
                case UssElement::NODE_BUTTON:
                    if($append) {
                        $value = $this->widget['value'] . ' ' . $value;
                    }
                    return $this->setWidgetValue($value);
            };
        }
        return $this->attributeSetter($this->widget['element'], $name, $value, $append);
    }

    public function getWidgetAttribute(string $name): ?string
    {
        return $this->widget['element']->getAttribute($name);
    }

    public function removeWidgetAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->widget['element'], $name, $detach);
    }

    public function setWidgetValue(?string $value): self
    {
        $this->widget['value'] = $value ?? '';
        $this->insertWidgetValue();
        return $this;
    }

    public function getWidgetValue(): ?string
    {
        return $this->widget['value'];
    }

    public function appendToWidget(null|string|UssElement $appendant): self
    {
        $this->widget['appendant'] = $this->refactorInputGroupContent($appendant);
        $this->extendWidgetAside(function () {
            $this->widgetContainer['element']->insertAfter(
                $this->widget['appendant'],
                $this->widget['element']
            );
        });
        return $this;
    }

    public function getWidgetAppendant(): ?UssElement
    {
        return $this->widget['appendant'];
    }

    public function prependToWidget(null|string|UssElement $prependant): self
    {
        $this->widget['prependant'] = $this->refactorInputGroupContent($prependant);
        $this->extendWidgetAside(function () {
            if($this->widget['prependant']) {
                $this->widgetContainer['element']->insertBefore(
                    $this->widget['prependant'],
                    $this->widget['element']
                );
            }
        });
        return $this;
    }

    public function getWidgetPrependant(): ?UssElement
    {
        return $this->widget['prependant'];
    }

    /**
     * For: Widget Modifier
     */
    public function setWidgetOptions(array $options): self
    {
        $this->widget['options']['values'] = $options;
        $this->rebuildWidgetOptionsElements($options);
        return $this;
    }

    public function setWidgetOption(string $key, string $displayValue): self
    {
        $optionElement = $this->widget['options']['elements'][$key] ?? null;
        if(!$optionElement) {
            $optionElement = $this->createOptionElement($key, $displayValue);
            $this->widget['element']->appendChild($optionElement);
        } else {
            $optionElement->setContent($displayValue);
        }
        $this->widget['options']['values'][$key] = $displayValue;
        $this->widget['options']['elements'][$key] = $optionElement;
        return $this;
    }

    public function removeWidgetOption(string $key): self
    {
        if($this->hasWidgetOption($key)) {
            $optionElement = $this->getWidgetOptionElement($key);
            unset($this->widget['options']['values'][$key]);
            unset($this->widget['options']['elements'][$key]);
            $optionElement->getParentElement()->removeChild($optionElement);
        }
        return $this;
    }

    public function getWidgetOptions(): array
    {
        return $this->widget['options'];
    }

    public function hasWidgetOption(string $key): bool
    {
        return array_key_exists($key, $this->widget['options']['values']);
    }

    public function getWidgetOptionElement(string $key): ?UssElement
    {
        return $this->widget['options']['elements'][$key] ?? null;
    }

    /**
     * @method otherWidgetMethods
     */
    public function setWidgetChecked(bool $status): self
    {
        if($this->isCheckable()) {
            if($status) {
                $this->widget['element']->setAttribute('checked', 'checked');
            } else {
                $this->widget['element']->removeAttribute('checked');
            }
        }
        return $this;
    }

    public function isWidgetChecked(): bool
    {
        return $this->isCheckable() && $this->widget['element']->hasAttribute('checked');
    }

    public function setDisabled(bool $status): self
    {
        if($status) {
            $this->widget['element']->setAttribute('disabled', 'disabled');
        } else {
            $this->widget['element']->removeAttribute('disabled');
        }
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->widget['element']->hasAttribute('disabled');
    }

    public function setReadonly(bool $status): self
    {
        if($status) {
            $this->widget['element']->setAttribute('readonly', 'readonly');
        } else {
            $this->widget['element']->removeAttribute('readonly');
        }
        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->widget['element']->hasAttribute('readonly');
    }


    public function isRequired(): bool
    {
        return $this->widget['element']->hasAttribute('required');
    }

    public function setRequired(bool $status): self
    {
        if($status) {
            $this->widget['element']->setAttribute('required', 'required');
        } else {
            $this->widget['element']->removeAttribute('required');
        }
        return $this;
    }

    public function setInfoHidden(bool $status): self
    {
        if($this->isHiddenWidget()) {
            $status = true;
        };
        $this->info['hidden'] = $status;

        if(!$this->info['hidden']) {
            $this->container['element']->insertBefore(
                $this->info['element'],
                $this->widgetContainer['element']
            );
        } else {
            $this->info['element']
                ->getParentElement()
                ->removeChild($this->info['element']);
        }

        return $this;
    }

    public function isInfoHidden(): bool
    {
        return $this->info['hidden'];
    }

    public function setLabelHidden(bool $status): self
    {
        if($this->isHiddenWidget()) {
            $status = true;
        };
        $this->label['hidden'] = $status;

        if(!$this->label['hidden'] && !$this->isButton()) {
            if(!$this->isCheckable()) {
                $this->container['element']->prependChild($this->label['element']);
            } else {
                $this->widgetContainer['element']->insertAfter(
                    $this->label['element'],
                    $this->widget['element']
                );
            }
        } else {
            $this->label['element']
                ->getParentElement()
                ->removeChild($this->label['element']);
        }

        return $this;
    }

    public function isLabelHidden(): bool
    {
        return $this->label['hidden'];
    }

    public function setValidationHidden(bool $status): self
    {
        if($this->isHiddenWidget()) {
            $status = true;
        };
        $this->validation['hidden'] = $status;
        if(!$this->validation['hidden'] && !$this->isButton()) {
            $this->container['element']->insertAfter(
                $this->validation['element'],
                $this->widgetContainer['element']
            );
        } else {
            $this->validation['element']
                ->getParentElement()
                ->removeChild($this->validation['element']);
        }
        return $this;
    }

    public function isValidationHidden(): bool
    {
        return $this->validation['hidden'];
    }

    /**
     * @method getFieldAsElement
     */
    public function getFieldAsElement(): UssElement
    {
        return $this->row['element'];
    }

    /**
     * @method getFieldAsHTML
     */
    public function getFieldAsHTML(): string
    {
        return $this->getFieldAsElement()->getHTML(true);
    }

    /**
     * @method createAlt
     */
    public function createAlt(string $name, string $type = UssForm::TYPE_HIDDEN): UssElement
    {
        $altTypes = [
            UssForm::TYPE_HIDDEN,
            UssForm::TYPE_CHECKBOX,
            UssForm::TYPE_RADIO,
            UssForm::TYPE_SWITCH
        ];

        if(!in_array($type, $altTypes)) {
            $type = UssForm::TYPE_HIDDEN;
        }

        $field = new self(UssForm::NODE_INPUT, $type);
        $altElement = $field->getWidgetContainerElement();

        if(!empty($this->widget['alt'])) {
            $prev = end($this->widget['alt']);
        } else {
            $prev = $this->widgetContainer['element'];
        }

        $this->widget['alt'][$name] = $altElement;
        $this->container['element']->insertAfter($altElement, $prev);
        return $altElement;
    }

    /**
     * @method getAlt
     */
    public function getAlt(string $name): ?UssElement
    {
        return $this->widget['alt'][$name] ?? null;
    }

    /**
     * @method removeAlt
     */
    public function removeAlt(string $name): ?UssElement
    {
        $altElement = $this->getAlt($name);
        if($altElement) {
            $altElement
                ->getParentElement()
                ->removeChild($altElement);
            unset($this->widget['alt'][$name]);
        };
        return $altElement;
    }

    /**
     * @method getAlts
     */
    public function getAlts(): array
    {
        return $this->widget['alt'];
    }
}
