<?php

namespace Ucscode\UssForm\Collection\Context;

use Ucscode\UssForm\Collection\Manifest\AbstractCollectionContext;

class SubtitleContext extends AbstractCollectionContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'collection-subtitle small');
    }
}
