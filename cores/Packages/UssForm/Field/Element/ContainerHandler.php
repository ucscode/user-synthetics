<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;
use Ucscode\UssForm\Resource\Context\Context;

class ContainerHandler extends AbstractFieldContext
{
    public function onCreate(Context $context): void
    {
        $context->getElement()->setAttribute('class', $this->defineClass());
    }

    protected function defineClass(): string
    {
        $class = 'widget-container';
        if($this->elementContext->widget->isCheckable()) {
            $class .= ' form-check';
            if($this->elementContext->getField()->nodeType === Field::TYPE_SWITCH) {
                $class .= ' form-switch';
            }
        }
        return $class;
    }
}
