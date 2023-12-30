<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Manifest\AbstractElementHandler;

class LabelHandler extends AbstractElementHandler
{
    public function onCreate(UssElement $element): void
    {
        $element->setAttribute(
            'class',
            $this->elementContext->widget->isCheckable() ? 'form-check-label' : 'form-label'
        );
    }
}