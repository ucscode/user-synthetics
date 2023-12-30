<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

abstract class AbstractCollectionContext extends AbstractContextResolver
{
    public function __construct(public readonly ElementContext $elementContext)
    {

    }
}
