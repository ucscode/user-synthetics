<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class InfoContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->addClass('field-info small');
    }
}
