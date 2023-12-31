<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElementNodeListInterface;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Gadget;

interface FieldInterface extends FieldTypesInterface, UssElementNodeListInterface
{
    public function getElementContext(): ElementContext;
    public function addGadget(string $name, Gadget $gadget): self;
    public function getGadget(string $name): Gadget;
    public function hasGadget(string|Gadget $gadget): bool;
    public function getGadgets(): array;
}
