<?php

namespace Ucscode\UssForm\Field\Foundation;

use stdClass;
use Ucscode\UssForm\Gadget\Gadget;

abstract class AbstractGadgetContext extends Gadget
{
    public function __construct(protected ElementContext $elementContext, protected stdClass $store)
    {
        $field = $elementContext->getField();
        parent::__construct($field->nodeName, $field->nodeType);
        array_walk($store, fn($value, $key) => $this->store->{$key} = $value);
    }
}
