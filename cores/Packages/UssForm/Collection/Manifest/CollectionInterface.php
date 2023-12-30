<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssForm\Field\Field;

interface CollectionInterface
{
    public function addField(string $name, Field $field): self;
    public function getField(string $name): ?Field;
    public function removeField(string $name): ?Field;
    public function getFields(): array;
    public function getElementContext(): ElementContext;
}
