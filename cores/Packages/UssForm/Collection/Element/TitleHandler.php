<?php

namespace Ucscode\UssForm\Collection\Element;

use Ucscode\UssForm\Collection\Manifest\AbstractCollectionContext;
use Ucscode\UssForm\Resource\Context\Context;

class TitleHandler extends AbstractCollectionContext
{
    public function onCreate(Context $context): void
    {
        $context->getElement()->setAttribute('class', 'title');
    }
}
