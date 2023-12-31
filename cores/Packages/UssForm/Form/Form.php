<?php

namespace Ucscode\UssForm\Form;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Form\Manifest\AbstractForm;

class Form extends AbstractForm
{
    public function addCollection(string $name, Collection $collection): self
    {
        $this->collections[$name] = $collection;
        $this->swapCollection(
            $collection->getElementContext()->fieldset->getElement()
        );
        return $this;
    }

    public function getCollection(string $name): ?Collection
    {
        return $this->collections[$name] ?? null;
    }
    
    public function removeCollection(string|Collection $context): ?Collection
    {
        return $context;
    }

    public function hasCollection(string|Collection $context): bool
    {
        return false;
    }

    public function getCollectionName(Collection $collection): ?string
    {
        return '';
    }

    public function getCollections(): array
    {
        return $this->collections;
    }

    public function getElement(): UssElement
    {
        return $this->element;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function export(): string
    {
        return $this->element->getHTML(true);
    }

    public function setCollectionPosition(string|Collection $collection, int $position, string|Collection $targetCollection): bool
    {
        return false;
    }
}
