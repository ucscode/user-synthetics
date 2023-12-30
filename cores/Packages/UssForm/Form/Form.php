<?php

namespace Ucscode\UssForm\Form;

use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Form\Attribute;
use Ucscode\UssForm\Form\Manifest\AbstractForm;

class Form extends AbstractForm
{
    protected array $collection = [];

    public function __construct(public readonly Attribute $attribute = new Attribute())
    {
        $this->addCollection("default", new Collection());
    }

    public function addCollection(string $name, Collection $collection): self
    {
        $this->collection[$name] = $collection;
        return $this;
    }

    public function getCollection(string $name): ?Collection
    {
        return $this->collection[$name] ?? null;
    }
}
