<?php

namespace Ucscode\UssForm\Collection\Context;

use Ucscode\UssForm\Collection\Manifest\AbstractCollectionContext;

class TitleContext extends AbstractCollectionContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'collection-title');
    }
}
