<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class ContainerContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->element->setAttribute('class', $this->defineClass());
    }

    protected function defineClass(): string
    {
        $class = 'widget-container input-single';
        if($this->elementContext->widget->isCheckable()) {
            $class .= ' form-check';
            if($this->elementContext->getField()->nodeType === Field::TYPE_SWITCH) {
                $class .= ' form-switch';
            }
        }
        return $class;
    }
}
