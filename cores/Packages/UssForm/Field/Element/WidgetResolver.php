<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

class WidgetResolver extends AbstractContextResolver
{
    public function onCreate(AbstractContext $context): void
    {
        // if($nodeType = $this->elementContext->getField()->nodeType) {
        //     $context->getElement()->setAttribute(
        //         'type',
        //         $nodeType == Field::TYPE_SWITCH ? Field::TYPE_CHECKBOX : $nodeType
        //     );
        // }
    }
}
