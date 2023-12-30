<?php

namespace Ucscode\UssForm\Collection\Manifest;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Collection\Context\ContainerContext;
use Ucscode\UssForm\Collection\Context\FieldsetContext;
use Ucscode\UssForm\Collection\Context\InstructionContext;
use Ucscode\UssForm\Collection\Context\SubtitleContext;
use Ucscode\UssForm\Collection\Context\TitleContext;
use Ucscode\UssForm\Resource\Context\AbstractElementContext;

/**
 * An ElementContext is a container that holds multiple predefined "Context" Object
 *
 * ElementContext for COLLECTION
 */
class ElementContext extends AbstractElementContext
{
    public readonly FieldsetContext $fieldset;
    public readonly TitleContext $title;
    public readonly SubtitleContext $subtitle;
    public readonly InstructionContext $instruction;
    public readonly ContainerContext $container;

    public function __construct(protected Collection $collection)
    {
        $store = new stdClass();

        $this->fieldset = new FieldsetContext(
            $this,
            UssElement::NODE_FIELDSET,
            $store
        );

        $this->title = new TitleContext(
            $this,
            UssElement::NODE_LEGEND,
            $store
        );

        $this->subtitle = new SubtitleContext(
            $this,
            UssElement::NODE_P,
            $store
        );

        $this->instruction = new InstructionContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->container = new ContainerContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->assembleContextElements();
        $this->visualizeContextElements();
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function export(): string
    {
        return 'EXPORTING COLLECTION ELEMENT CONTEXT';
    }

    public function visualizeContextElements(): void
    {
        
    }

    protected function assembleContextElements(): void
    {
        
    }
}
