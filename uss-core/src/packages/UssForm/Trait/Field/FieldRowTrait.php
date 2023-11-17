<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;

trait FieldRowTrait
{
    protected array $row = [
        'element' => null,
    ];

    public function getRowElement(): UssElement
    {
        return $this->row['element'];
    }

    public function setRowAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->row['element'], $name, $value, $append);
    }

    public function getRowAttribute(string $name): ?string
    {
        return $this->row['element']->getAttribute($name);
    }

    public function removeRowAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->row['element'], $name, $detach);
    }
}
