<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;
use Ucscode\UssForm\Resource\Context\Context;

class WidgetHandler extends AbstractFieldContext
{
    public function onCreate(Context $context): void
    {
        if($nodeType = $this->elementContext->getField()->nodeType) {
            $context->getElement()->setAttribute(
                'type',
                $nodeType == Field::TYPE_SWITCH ? Field::TYPE_CHECKBOX : $nodeType
            );
        }
    }
}
