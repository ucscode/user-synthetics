<?php

namespace Ucscode\UssForm\Gadget\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Gadget\Foundation\AbstractGadgetContext;

class ContainerContext extends AbstractGadgetContext
{
    public function created(): void
    {
        $class = 'widget-container input-single';
        if($this->gadget->widget->isCheckable()) {
            $class .= ' form-check';
            if($this->gadget->widget->nodeType === Field::TYPE_SWITCH) {
                $class .= ' form-switch';
            }
        }
        $this->addClass($class);
    }

    public function setValue(string|UssElement|null $value): self
    {
        return $this;
    }

    public function setDOMHidden(bool $value): self
    {
        return $this;
    }
}