<?php

namespace Ucscode\UssForm\Collection\Element;

use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

class WrapperResolver extends AbstractContextResolver
{
    public function onCreate(AbstractContext $context): void
    {
        $context->getElement()->setAttribute('class', 'collection wrapper col-12');
    }
}
