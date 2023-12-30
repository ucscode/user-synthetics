<?php

namespace Ucscode\UssForm\Resource\Context;

use Ucscode\UssElement\UssElement;

abstract class AbstractContext
{
    public function onCreate(UssElement $element): void 
    {

    }

    public function onSetHidden(bool $value, Context $context): void 
    {

    }

    public function onIsHidden(Context $context): bool
    {
        return false;
    }

    public function onSetValue(?string $value, Context $context): void 
    {

    }
    
    public function onGetValue(Context $context): ?string
    {
        return null;
    }
}