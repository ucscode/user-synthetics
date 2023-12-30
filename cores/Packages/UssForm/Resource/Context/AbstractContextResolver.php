<?php

namespace Ucscode\UssForm\Resource\Context;

abstract class AbstractContextResolver
{
    abstract public function onCreate(Context $context): void;

    public function onSetHidden(bool $value, Context $context): void
    {
        $context->getElement()->setInvisible($value);
    }

    public function onIsHidden(Context $context): bool
    {
        return $context->getElement()->isInvisible();
    }

    public function onSetValue(?string $value, Context $context): void
    {
        $context->getElement()->setContent($value);
    }

    public function onGetValue(Context $context): ?string
    {
        return $context->getElement()->getContent();
    }
}
