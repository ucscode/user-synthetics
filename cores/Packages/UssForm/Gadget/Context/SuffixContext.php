<?php

namespace Ucscode\UssForm\Gadget\Context;

class SuffixContext extends PrefixContext
{
    protected string $name = 'suffix';
    
    protected function created(): void
    {
        parent::created();
        $this->store->suffixAppended = false;
    }

    protected function coupleElement(bool $valueIsButton): void
    {
        $container = $this->gadget->container->getElement();
        $container->appendChild($valueIsButton ? $this->value : $this->element);
    }
}