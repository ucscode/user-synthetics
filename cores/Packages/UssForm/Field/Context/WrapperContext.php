<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class WrapperContext extends AbstractFieldContext
{
    protected function created(): void
    {
        $this->element->setAttribute('class', 'field-wrapper');
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
