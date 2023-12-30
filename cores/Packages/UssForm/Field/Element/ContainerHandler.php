<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\AbstractElementHandler;

class ContainerHandler extends AbstractElementHandler
{
    public function onCreate(UssElement $element): void
    {
        $element->setAttribute('class', $this->defineClass());
    }

    protected function defineClass(): string
    {
        $class = 'widget-container';
        if($this->elementContext->widget->isCheckable()) {
            $class .= ' form-check';
            if($this->elementContext->field->nodeType === Field::TYPE_SWITCH) {
                $class .= ' form-switch';
            }
        }
        return $class;
    }
}