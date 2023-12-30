<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;
use Ucscode\UssForm\Resource\Context\Context;

class ValidationHandler extends AbstractFieldContext
{
    public function onCreate(Context $context): void
    {
        $context->getElement()->setAttribute('class', 'validation small');
    }
}
