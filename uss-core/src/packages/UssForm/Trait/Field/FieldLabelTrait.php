<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;

trait FieldLabelTrait
{
    protected array $label = [
        'element' => null,
        'value' => null,
        'hidden' => false
    ];

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
}
