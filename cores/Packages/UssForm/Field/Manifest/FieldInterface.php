<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElementNodeListInterface;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Gadget;

interface FieldInterface extends FieldTypesInterface, UssElementNodeListInterface
{
    public function getElementContext(): ElementContext;
    public function addGadget(string $name, Gadget $Gadget): self;
    public function getGadget(string $name): Gadget;
    public function hasGadget(string|Gadget $Gadget): bool;
    public function getGadgets(): array;
}
