<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

class ValidationResolver extends AbstractContextResolver
{
    public function onCreate(AbstractContext $context): void
    {
        $context->getElement()->setAttribute('class', 'validation small');
    }
}
