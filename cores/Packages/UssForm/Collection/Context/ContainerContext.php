<?php

namespace Ucscode\UssForm\Collection\Context;

use Ucscode\UssForm\Collection\Manifest\AbstractCollectionContext;

class ContainerContext extends AbstractCollectionContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'row container');
    }
}
