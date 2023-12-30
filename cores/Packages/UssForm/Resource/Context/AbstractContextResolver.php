<?php

namespace Ucscode\UssForm\Resource\Context;

abstract class AbstractContextResolver
{
    abstract public function onCreate(AbstractContext $context): void;

    public function onSetDOMHidden(bool $value, AbstractContext $context): void
    {
        $context->getElement()->setInvisible($value);
    }

    public function onIsDOMHidden(AbstractContext $context): bool
    {
        return $context->getElement()->isInvisible();
    }

    public function onSetValue(?string $value, AbstractContext $context): void
    {
        $context->getElement()->setContent($value);
    }

    public function onGetValue(AbstractContext $context): ?string
    {
        return $context->getElement()->getContent();
    }
}
