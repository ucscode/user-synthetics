<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class ValidationContext extends AbstractFieldContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'validation small');
    }
}
