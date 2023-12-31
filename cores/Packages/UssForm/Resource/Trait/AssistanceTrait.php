<?php

namespace Ucscode\UssForm\Resource\Trait;

use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Foundation\AbstractGadgetContext;
use Ucscode\UssForm\Gadget\Gadget;

trait AssistanceTrait
{
    protected function getLocalElementContext(): Gadget|ElementContext
    {
        return $this instanceof AbstractGadgetContext ? $this->gadget : $this->elementContext;
    }
}