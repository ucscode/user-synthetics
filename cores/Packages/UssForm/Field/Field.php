<?php

namespace Ucscode\UssForm\Field;

use Ucscode\UssForm\Field\Manifest\AbstractField;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Gadget;

class Field extends AbstractField
{
    protected array $Gadgets = [];

    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }

    public function addGadget(string $name, Gadget $Gadget): self
    {
        $this->Gadgets[$name] = $Gadget;
        return $this;
    }

    public function getGadget(string $name): Gadget
    {
        return $this->Gadgets[$name] ?? null;
    }

    public function hasGadget(string|Gadget $Gadget): bool
    {
        return false;
    }

    public function getGadgets(): array
    {
        return $this->Gadgets;
    }
}
