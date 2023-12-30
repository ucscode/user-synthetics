<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class InfoContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->element->setAttribute('class', 'info small');
    }
}
