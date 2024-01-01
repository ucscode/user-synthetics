<?php

namespace Ucscode\UssForm\Collection\Foundation;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Collection\Context\ContainerContext;
use Ucscode\UssForm\Collection\Context\FieldsetContext;
use Ucscode\UssForm\Collection\Context\InstructionContext;
use Ucscode\UssForm\Collection\Context\SubtitleContext;
use Ucscode\UssForm\Collection\Context\TitleContext;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssForm\Resource\Context\AbstractElementContext;

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
        $fields = $this->collection->getFields();

        array_walk(
            $fields,
            fn (Field $field) => $field->getElementContext()->export()
        );

        $contexts = array_intersect_key(
            $this->getAllContext(), 
            array_flip([
                'title', 
                'subtitle', 
                'instruction',
            ])
        );

        array_walk($contexts, function(AbstractContext $context) {
            !$context->hasValue() && !$context->isFixed() ? $context->addClass("d-none") : null;
        });

        return $this->fieldset->getElement()->getHTML(true);
    }

    public function visualizeContextElements(): void
    {

    }

    protected function assembleContextElements(): void
    {
        $elements = $this->getContextElements();
        $elements['fieldset']->appendChild($elements['title']);
        $elements['fieldset']->appendChild($elements['subtitle']);
        $elements['fieldset']->appendChild($elements['instruction']);
        $elements['fieldset']->appendChild($elements['container']);
    }
}
