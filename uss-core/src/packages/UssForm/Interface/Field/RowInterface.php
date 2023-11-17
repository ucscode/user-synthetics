<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface RowInterface
{
    public function getRowElement(): UssElement;
    public function setRowAttribute(string $name, ?string $value, bool $append): self;
    public function getRowAttribute(string $name): ?string;
    public function removeRowAttribute(string $name, ?string $detach): self;
}
