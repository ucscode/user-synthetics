<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Manifest\AbstractElementHandler;

class WrapperHandler extends AbstractElementHandler
{
    public function onCreate(UssElement $element): void
    {
        $element->setAttribute('class', 'wrapper');
    }
}