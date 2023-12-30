<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class WrapperContext extends AbstractFieldContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'field-wrapper');
    }
}
