<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

abstract class AbstractFieldContext extends AbstractContextResolver
{
    public function __construct(public readonly ElementContext $elementContext)
    {

    }
}
