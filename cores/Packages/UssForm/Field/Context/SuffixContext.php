<?php

namespace Ucscode\UssForm\Field\Context;

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
        $this->elementContext
            ->container->getElement()
            ->appendChild(
                $valueIsButton ? $this->value : $this->element
            );
    }
}