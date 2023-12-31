<?php

namespace Ucscode\UssForm\Field\Foundation;

use stdClass;
use Ucscode\UssForm\Resource\Context\AbstractContext;

abstract class AbstractFieldContext extends AbstractContext
{
    public function __construct(protected ElementContext $elementContext, string $nodeName, protected stdClass $store)
    {
        parent::__construct($nodeName);
    }
}
