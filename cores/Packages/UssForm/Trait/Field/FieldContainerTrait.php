<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;

trait FieldContainerTrait
{
    protected array $container = [
        'element' => null,
    ];

    public function getContainerElement(): UssElement
    {
        return $this->container['element'];
    }

    public function setContainerAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->container['element'], $name, $value, $append);
    }

    public function getContainerAttribute(string $name): ?string
    {
        return $this->container['element']->getAttribute($name);
    }

    public function removeContainerAttribute(string $name, ?string $detach = null): self
    {
        $this->attributeRemover($this->container['element'], $name, $detach);
        return $this;
    }
}
