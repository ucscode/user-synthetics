<?php

namespace Ucscode\UssForm\Trait\Fieldstack;

use Ucscode\UssElement\UssElement;

trait FieldstackElementTrait
{
    protected array $elements = [];

    /**
     * @method addElement
     */
    public function addElement(string $name, UssElement $element): self
    {
        $this->elements[$name] = $element;
        // Real time update
        $this->innerContainer['element']->appendChild($element);
        return $this;
    }

    /**
     * @method getElements
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @method getElement
     */
    public function getElement(string $name): ?UssElement
    {
        return $this->elements[$name] ?? null;
    }

    /**
     * @method removeElement
     */
    public function removeElement(string $name): self
    {
        if(array_key_exists($name, $this->elements)) {
            $element = $this->getElement($name);
            unset($this->elements[$name]);
            if($element && $element->getParentElement() === $this->innerContainer['element']) {
                $this->innerContainer['element']->removeChild($element);
            }
        }
        return $this;
    }
}
