<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\AbstractElementHandler;

class WidgetHandler extends AbstractElementHandler
{
    public function onCreate(UssElement $element): void
    {
        if($nodeType = $this->elementContext->field->nodeType) {
            $element->setAttribute(
                'type', 
                $nodeType == Field::TYPE_SWITCH ? Field::TYPE_CHECKBOX : $nodeType
            );
        }
    }
}