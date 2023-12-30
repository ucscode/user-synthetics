<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class FrameContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->element->setAttribute('class', 'frame col-12 my-1');
    }
}