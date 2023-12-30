<?php

namespace Ucscode\UssForm\Collection\Manifest;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Resource\Context\AbstractContext;

abstract class AbstractCollectionContext extends AbstractContext
{
    public function __construct(
        protected ElementContext $elementContext, 
        string|UssElement $element,
        protected stdClass $store
    )
    {
        parent::__construct($element, $store);
    }
}