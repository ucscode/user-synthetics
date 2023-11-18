<?php

namespace Ucscode\UssForm\Trait\Fieldstack;

use Ucscode\UssElement\UssElement;

trait FieldstackContainerTrait
{
    /**
     * @method getOuterContainerElement
     */
    public function getOuterContainerElement(): UssElement
    {
        return $this->outerContainer['element'];
    }

    /**
     * @method setOuterContainerAttribute
     */
    public function setOuterContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->outerContainer['element'], $name, $value, $append);
    }

    /**
     * @method getOuterContainerAttribute
     */
    public function getOuterContainerAttribute(string $name): ?string
    {
        return $this->outerContainer['element']->getAttribute($name);
    }

    /**
     * @method removeOuterContainerAttribute
     */
    public function removeOuterContainerAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->outerContainer['element'], $name, $detach);
    }

    /**
    * @method getInnerContainerElement
    */
    public function getInnerContainerElement(): UssElement
    {
        return $this->innerContainer['element'];
    }

    /**
     * @method setInnerContainerAttribute
     */
    public function setInnerContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->innerContainer['element'], $name, $value, $append);
    }

    /**
     * @method getInnerContainerAttribute
     */
    public function getInnerContainerAttribute(string $name): ?string
    {
        return $this->innerContainer['element']->getAttribute($name);
    }

    /**
     * @method removeInnerContainerAttribute
     */
    public function removeInnerContainerAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->innerContainer['element'], $name, $detach);
    }
}
