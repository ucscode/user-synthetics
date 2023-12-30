<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssForm\Resource\Context\AbstractContext;

abstract class AbstractElementHandler extends AbstractContext
{
    public function __construct(public readonly ElementContext $elementContext)
    {
        
    }
}