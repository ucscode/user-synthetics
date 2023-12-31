<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssForm\Collection\Foundation\ElementContext;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Facade\PositionInterface;

interface CollectionInterface extends PositionInterface
{
    public function addField(string $name, Field $field): self;
    public function getField(string $name): ?Field;
    public function removeField(string|Field $context): ?Field;
    public function hasField(string|Field $context): bool;
    public function getFieldName(Field $field): ?string;
    public function getFields(): array;
    public function getElementContext(): ElementContext;
    public function setFieldPosition(string|Field $field, int $position, string|Field $targetField): bool;
}
