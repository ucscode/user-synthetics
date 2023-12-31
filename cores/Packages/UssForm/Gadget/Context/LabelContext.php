<?php

namespace Ucscode\UssForm\Gadget\Context;

use Ucscode\UssForm\Gadget\Foundation\AbstractGadgetContext;

class LabelContext extends AbstractGadgetContext
{
    public function created(): void
    {
        $this->addClass(
            $this->gadget->widget->isCheckable() ? 'form-check-label' : 'form-label'
        );
    }
}