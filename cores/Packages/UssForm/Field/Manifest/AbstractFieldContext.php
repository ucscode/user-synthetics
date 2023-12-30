<?php

namespace Ucscode\UssForm\Field\Manifest;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Manifest\ElementContext;
use Ucscode\UssForm\Resource\Context\AbstractContext;

abstract class AbstractFieldContext extends AbstractContext
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