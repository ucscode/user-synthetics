<?php

namespace Ucscode\UssForm\Field\Element;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Manifest\AbstractElementHandler;

class FrameHandler extends AbstractElementHandler
{
    public function onCreate(UssElement $element): void
    {
        $element->setAttribute('class', 'frame col-12 my-1');
    }
}