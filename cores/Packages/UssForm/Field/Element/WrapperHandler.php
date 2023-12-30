<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;
use Ucscode\UssForm\Resource\Context\Context;

class WrapperHandler extends AbstractFieldContext
{
    public function onCreate(Context $context): void
    {
        $context->getElement()->setAttribute('class', 'wrapper');
    }
}
