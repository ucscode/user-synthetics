<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\UssFormField;

trait FieldWidgetTrait
{
    protected array $widget = [
        'element' => null,
        'value' => null,
        'appendant' => null,
        'prependant' => null,
        'options' => [
            'values' => [],
            'elements' => [],
        ],
        'alt' => []
    ];

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

    /**
     * @method isHiddenWidget
     */
    public function isWidgetHidden(): bool
    {
        return
            $this->nodeName === UssElement::NODE_INPUT &&
            $this->nodeType === UssForm::TYPE_HIDDEN;
    }

    /**
     * @method isCheckable
     */
    public function isCheckable(): bool
    {
        return $this->nodeName === UssElement::NODE_INPUT &&
        in_array($this->nodeType, [
            UssForm::TYPE_CHECKBOX,
            UssForm::TYPE_RADIO,
            UssForm::TYPE_SWITCH
        ]);
    }

    /**
     * @method isButton
     */
    public function isButton(): bool
    {
        if($this->nodeName === UssElement::NODE_BUTTON) {
            return true;
        } elseif($this->nodeName === UssElement::NODE_INPUT) {
            return in_array(
                $this->nodeType,
                [
                    UssForm::TYPE_SUBMIT,
                    UssForm::TYPE_BUTTON
                ]
            );
        }
        return false;
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

        $field = new UssFormField(UssForm::NODE_INPUT, $type);
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
