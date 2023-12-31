<?php

namespace Ucscode\UssForm\Field\Foundation;

use stdClass;
use Ucscode\UssForm\Widget\Foundation\AbstractWidgetContext;

abstract class AbstractFieldWidgetContext extends AbstractWidgetContext
{
    public function __construct(protected ElementContext $elementContext, protected stdClass $store)
    {
        $field = $elementContext->getField();
        parent::__construct($field->nodeName, $field->nodeType, $store);
    }
}
