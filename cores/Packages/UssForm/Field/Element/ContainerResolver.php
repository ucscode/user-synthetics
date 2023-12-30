<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

class ContainerResolver extends AbstractContextResolver
{
    public function onCreate(AbstractContext $context): void
    {
        $context->getElement()->setAttribute('class', $this->defineClass());
    }

    protected function defineClass(): string
    {
        $class = 'widget-container input-single';
        // if($this->elementContext->widget->isCheckable()) {
        //     $class .= ' form-check';
        //     if($this->elementContext->getField()->nodeType === Field::TYPE_SWITCH) {
        //         $class .= ' form-switch';
        //     }
        // }
        return $class;
    }
}
