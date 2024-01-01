<?php

namespace Ucscode\UssForm\Form;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Form\Manifest\AbstractForm;
use Ucscode\UssForm\Resource\Facade\Position;

class Form extends AbstractForm
{
    public function addCollection(string $name, Collection $collection): self
    {
        $oldCollection = $this->getCollection($name);
        $this->collections[$name] = $collection;
        $this->swapCollection(
            $collection->getElementContext()->fieldset->getElement(),
            $oldCollection?->getElementContext()->fieldset->getElement()
        );
        $this->welcomeCollection($name, $collection);
        return $this;
    }

    public function getCollection(string $name): ?Collection
    {
        return $this->collections[$name] ?? null;
    }
    
    public function removeCollection(string|Collection $context): ?Collection
    {
        if($this->hasCollection($context)) {
            $collection = $context instanceof Collection ? $context : $this->getCollection($context);
            $name = $this->getCollectionName($collection);
            unset($this->collections[$name]);
            $element = $collection->getElementContext()->fieldset->getElement();
            $element->getParentElement()->removeChild($element);
            return $collection;
        }
        return null;
    }

    public function hasCollection(string|Collection $context): bool
    {
        if($context instanceof Collection) {
            return array_search($context, $this->collections, true) !== false;
        }
        return !!$this->getCollection($context);
    }

    public function getCollectionName(Collection $collection): ?string
    {
        $name = array_search($collection, $this->collections, true);
        return $name !== false ? $name : null;
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
        array_walk(
            $this->collections, 
            fn (Collection $collection) => $collection->getElementContext()->export()
        );
        return $this->element->getHTML(true);
    }

    public function setCollectionPosition(string|Collection $collection, Position $position, string|Collection $targetCollection): bool
    {
        $collection = $collection instanceof Collection ? $collection : $this->getCollection($collection);
        $targetCollection = $targetCollection instanceof Collection ? $targetCollection : $this->getCollection($targetCollection);

        if($this->hasCollection($collection) && $this->hasCollection($targetCollection)) {
            
            $collectionElement = $collection->getElementContext()->fieldset->getElement();
            $targetElement = $targetCollection->getElementContext()->fieldset->getElement();

            $position === Position::AFTER ?
                $this->element->insertAfter($collectionElement, $targetElement) :
                $this->element->insertBefore($collectionElement, $targetElement);

            return true;
        }

        return false;
    }
}
