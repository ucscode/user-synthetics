<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class ContainerContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->element->setAttribute('class', $this->defineClass());
    }

    public function setValue(string|UssElement|null $value): self
    {
        return $this;
    }

    public function setDOMHidden(bool $value): self
    {
        return $this;
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
