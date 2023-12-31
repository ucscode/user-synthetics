<?php

namespace Ucscode\UssForm\Field;

use Ucscode\UssForm\Field\Manifest\AbstractField;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Gadget;

class Field extends AbstractField
{
    protected array $gadgets = [];

    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }

    public function addGadget(string $name, Gadget $gadget): self
    {
        $this->gadgets[$name] = $gadget;
        $this->swapField(
            $gadget->container->getElement()
        );
        return $this;
    }

    public function getGadget(string $name): Gadget
    {
        return $this->gadgets[$name] ?? null;
    }

    public function hasGadget(string|Gadget $gadget): bool
    {
        return false;
    }

    public function getGadgets(): array
    {
        return $this->gadgets;
    }
}
