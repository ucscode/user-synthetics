<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class LabelContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->element->setAttribute(
            'class',
            $this->elementContext->widget->isCheckable() ? 'form-check-label' : 'form-label'
        );
    }
}
