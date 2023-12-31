<?php

namespace Ucscode\UssForm\Gadget\Foundation;

use stdClass;
use Ucscode\UssForm\Gadget\Gadget;
use Ucscode\UssForm\Resource\Context\AbstractContext;

abstract class AbstractGadgetContext extends AbstractContext
{
    public function __construct(protected Gadget $gadget, string $nodeName, protected stdClass $store)
    {
        parent::__construct($nodeName);
    }
}