<?php

namespace Ucscode\UssForm\Gadget\Foundation;

use stdClass;
use Ucscode\UssForm\Gadget\Gadget;
use Ucscode\UssForm\Resource\Context\AbstractWidgetContext;

abstract class AbstractGadgetWidgetContext extends AbstractWidgetContext
{
    public function __construct(protected Gadget $gadget, protected stdClass $store)
    {
        parent::__construct($gadget->nodeName, $gadget->nodeType);
    }
}