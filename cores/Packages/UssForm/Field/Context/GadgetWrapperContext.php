<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class GadgetWrapperContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->addClass("gadget-wrapper");
    }
}