<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Collection\Context\CollectionContext;
use Ucscode\UssForm\Collection\Element\ContainerResolver;
use Ucscode\UssForm\Collection\Element\InstructionResolver;
use Ucscode\UssForm\Collection\Element\SubTitleResolver;
use Ucscode\UssForm\Collection\Element\TitleResolver;
use Ucscode\UssForm\Collection\Element\WrapperResolver;
use Ucscode\UssForm\Resource\Context\AbstractElementContext;

/**
 * An ElementContext is a container that holds multiple predefined "Context" Object
 *
 * ElementContext for COLLECTION
 */
class ElementContext extends AbstractElementContext
{
    public readonly CollectionContext $fieldset;
    public readonly CollectionContext $title;
    public readonly CollectionContext $subtitle;
    public readonly CollectionContext $instruction;
    public readonly CollectionContext $container;

    public function __construct(protected Collection $collection)
    {
        $this->fieldset = new CollectionContext(
            UssElement::NODE_FIELDSET,
            new WrapperResolver($this)
        );

        $this->title = new CollectionContext(
            UssElement::NODE_LEGEND,
            new TitleResolver($this)
        );

        $this->subtitle = new CollectionContext(
            UssElement::NODE_P,
            new SubTitleResolver($this)
        );

        $this->instruction = new CollectionContext(
            UssElement::NODE_DIV,
            new InstructionResolver($this)
        );

        $this->container = new CollectionContext(
            UssElement::NODE_DIV,
            new ContainerResolver($this)
        );

        $this->groupContextElements();
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    protected function groupContextElements(): void
    {
        
    }
}
