<?php

namespace Ucscode\UssForm\Collection\Context;

use Ucscode\UssForm\Collection\Manifest\AbstractCollectionContext;

class InstructionContext extends AbstractCollectionContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'instruction alert alert-info');
    }
}
