<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class FrameContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->addClass('frame col-12 my-1');
    }

    public function setValue(string|UssElement|null $value): self
    {
        return $this;
    }
}
