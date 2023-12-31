<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElementNodeListInterface;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Gadget;
use Ucscode\UssForm\Resource\Facade\Position;

interface FieldInterface extends FieldTypesInterface, UssElementNodeListInterface
{
    public function getElementContext(): ElementContext;
    public function addGadget(string $name, Gadget $gadget): self;
    public function getGadget(string $name): ?Gadget;
    public function removeGadget(string|Gadget $context): ?Gadget;
    public function getGadgetName(Gadget $gadget): ?string;
    public function hasGadget(string|Gadget $gadget): bool;
    public function getGadgets(): array;
    public function setGadgetPosition(string|Gadget $gadget, Position $position, string|Gadget $targetGadget): bool;
}
