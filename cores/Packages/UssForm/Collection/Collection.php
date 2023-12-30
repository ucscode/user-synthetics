<?php

namespace Ucscode\UssForm\Collection;

use Ucscode\UssForm\Collection\Manifest\AbstractCollection;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Collection\Manifest\ElementContext;

class Collection extends AbstractCollection
{
    protected array $fields = [];
    protected ElementContext $elementContext;

    public function __construct()
    {
        $this->elementContext = new ElementContext($this);
    }

    public function addField(string $name, Field $field): self
    {
        $this->fields[$name] = $field;
        return $this;
    }

    public function getField(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    public function removeField(string $name): ?Field
    {
        $field = $this->getField($name);
        if($field) {
            unset($this->fields[$name]);
        }
        return $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }
}
