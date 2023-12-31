<?php

namespace Ucscode\UssForm\Collection\Foundation;

use stdClass;
use Ucscode\UssForm\Resource\Context\AbstractContext;

abstract class AbstractCollectionContext extends AbstractContext
{
    public function __construct(protected ElementContext $elementContext, string $element, protected stdClass $store) 
    {
        parent::__construct($element);
    }
}
