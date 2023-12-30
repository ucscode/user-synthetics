<?php

namespace Ucscode\UssForm\Resource\Context;

use stdClass;
use Ucscode\UssElement\UssElement;

abstract class AbstractContext
{
    abstract protected function created();
    
    protected UssElement $element;
    protected UssElement|string|null $value = null;

    public function __construct(string|UssElement $element, protected stdClass $store) 
    {
        $this->element = $element instanceof UssElement ? $element : new UssElement($element);
        $this->created();
    }

    public function setAttribute(string $name, ?string $value, bool $append = false): self
    {
        $append ?
            $this->element->addAttributeValue($name, $value) :
            $this->element->setAttribute($name, $value);
        return $this;
    }

    public function getAttribute(string $name): ?string
    {
        return $this->element->getAttribute($name);
    }

    public function removeAttribute(string $name, ?string $value = null): self
    {
        $value !== null ?
            $this->element->removeAttributeValue($name, $value) :
            $this->element->removeAttribute($name);
        return $this;
    }

    public function setDOMHidden(bool $value): self
    {
        $this->element->setInvisible($value);
        return $this;
    }

    public function isDOMHidden(): bool
    {
        return $this->element->isInvisible();
    }

    public function setValue(string|UssElement|null $value): self
    {
        $this->value = $value;
        $this->element->freeElement();
        $value instanceof UssElement ? $this->element->appendChild($value) : $this->element->setContent($value);
        return $this;
    }

    public function getValue(): null|UssElement|string
    {
        return $this->value;
    }

    public function getElement(): UssElement
    {
        return $this->element;
    }
}
