<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssForm\Resource\Context\AbstractContextResolver;

class LabelResolver extends AbstractContextResolver
{
    public function onCreate(AbstractContext $context): void
    {
        $element = $context->getElement();
        // $element->setAttribute(
        //     'class',
        //     $this->elementContext->widget->isCheckable() ? 'form-check-label' : 'form-label'
        // );
        $element->setAttribute('for');
    }
}
