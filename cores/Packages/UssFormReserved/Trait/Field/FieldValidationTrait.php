<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\Field\ValidationInterface;

trait FieldValidationTrait
{
    protected array $validation = [
        'element' => null,
        'value' => null,
        'type' => ValidationInterface::VALIDATION_ERROR,
        'icon' => null,
        'hidden' => false
    ];

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

    public function setValidationHidden(bool $status): self
    {
        if($this->isWidgetHidden()) {
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
}
