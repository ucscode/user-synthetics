<?php

namespace Ucscode\UssForm\Collection\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Foundation\AbstractCollectionContext;

class ContainerContext extends AbstractCollectionContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'row collection-container');
    }

    public function setValue(string|UssElement|null $value): self
    {
        return $this;
    }

    public function setDOMHidden(bool $value): self
    {
        return $this;
    }
}
