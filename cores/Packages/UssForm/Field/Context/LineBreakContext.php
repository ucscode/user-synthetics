<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class LineBreakContext extends AbstractFieldContext
{
    public function created(): void
    {
        $this->addClass("field-break col-12");
    }

    public function setValue(string|UssElement|null $value): self
    {
        return $this;
    }
}