<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class LabelContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->addClass(
            $this->elementContext->widget->isCheckable() ? 'form-check-label' : 'form-label'
        );
    }
}
