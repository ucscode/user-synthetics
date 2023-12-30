<?php

namespace Ucscode\UssForm\Resource\Context;

use Ucscode\UssElement\UssElement;

abstract class AbstractContext
{
    protected UssElement $element;

    public function __construct(string|UssElement $element, protected AbstractContextResolver $contextResolver) 
    {
        $this->element = $element instanceof UssElement ? $element : new UssElement($element);
        $this->contextResolver->onCreate($this);
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

    public function setValue(?string $value): self
    {
        $this->contextResolver->onSetValue($value, $this);
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->contextResolver->onGetValue($this);
    }

    public function setDOMHidden(bool $hidden = true): self
    {
        $this->contextResolver->onSetDOMHidden($hidden, $this);
        return $this;
    }

    public function isDOMHidden(): bool
    {
        return $this->contextResolver->onIsDOMHidden($this);
    }

    public function getElement(): UssElement
    {
        return $this->element;
    }
}
