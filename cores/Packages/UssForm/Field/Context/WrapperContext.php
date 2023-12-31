<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;

class WrapperContext extends AbstractFieldContext
{
    protected function created(): void
    {
        $this->addClass('field-wrapper');
    }

    public function setValue(string|UssElement|null $value): self
    {
        return $this;
    }

    public function setDOMHidden(bool $value): self
    {
        return $this;
    }
}
