<?php

namespace Ucscode\UssForm\Resource\Context;

use Ucscode\UssElement\UssElement;

/**
 * A Context is an API for manipulating object in an isolated manner
 * It defines custom logics to which a particular element can be controlled
 */
class Context
{
    protected UssElement $element;

    public function __construct(
        string|UssElement $element, 
        protected AbstractContext $abstractContext
    ) 
    {
        $this->element = $element instanceof UssElement ? $element : new UssElement($element);
        $this->abstractContext->onCreate($this->element, $this);
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
        $this->abstractContext->onSetValue($value, $this);
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->abstractContext ? $this->abstractContext->onGetValue($this) : null;
    }

    public function setHidden(bool $hidden): self
    {
        $this->abstractContext->onSetHidden($hidden, $this);
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->abstractContext->onIsHidden($this);
    }
    
    public function getElement(): UssElement
    {
        return $this->element;
    }
}