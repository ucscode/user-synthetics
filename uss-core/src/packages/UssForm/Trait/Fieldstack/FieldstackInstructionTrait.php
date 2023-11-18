<?php

namespace Ucscode\UssForm\Trait\Fieldstack;

use Ucscode\UssElement\UssElement;

trait FieldstackInstructionTrait
{
    /**
     * @method getInstructionElement
     */
    public function getInstructionElement(): UssElement
    {
        return $this->instruction['element'];
    }

    /**
     * @method setInstructionAttribute
     */
    public function setInstructionAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->instruction['element'], $name, $value, $append);
    }

    /**
     * @method getInstructionAttribute
     */
    public function getInstructionAttribute(string $name): ?string
    {
        return $this->instruction['element']->getAttribute($name);
    }

    /**
     * @method removeInstructionAttribute
     */
    public function removeInstructionAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->instruction['element'], $name, $detach);
    }

    /**
     * @method setInstructionValue
     */
    public function setInstructionValue(null|string|UssElement $value): self
    {
        return $this->valueSetter($this->instruction, $value);
    }

    /**
     * @method getInstructionValue
     */
    public function getInstructionValue(): UssElement|string|null
    {
        return $this->instruction['value'];
    }

    /**
     * @method hideInstruction
     */
    public function hideInstruction(bool $status): self
    {
        $this->instruction['hidden'] = $status;
        if($this->instruction['hidden']) {
            $this->hideElement($this->instruction['element']);
        } else {
            $this->outerContainer->insertBefore(
                $this->instruction['element'],
                $this->innerContainer['element']
            );
        }
        return $this;
    }

    /**
     * @method isInstructionHidden
     */
    public function isInstructionHidden(): bool
    {
        if($this->instruction['value'] && !$this->instruction['hidden']) {

        }
        return $this->instruction['hidden'];
    }
}
