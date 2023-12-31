<?php

namespace Ucscode\UssForm\Collection\Context;

use Ucscode\UssForm\Collection\Foundation\AbstractCollectionContext;

class SubtitleContext extends AbstractCollectionContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'collection-subtitle small');
    }
}
