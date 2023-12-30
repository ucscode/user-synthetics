<?php

namespace Ucscode\UssForm\Collection\Manifest;

use PHPUnit\TestRunner\TestResult\Collector;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Collection\Element\ContainerHandler;
use Ucscode\UssForm\Collection\Element\InstructionHandler;
use Ucscode\UssForm\Collection\Element\SubTitleHandler;
use Ucscode\UssForm\Collection\Element\TitleHandler;
use Ucscode\UssForm\Collection\Element\WrapperHandler;
use Ucscode\UssForm\Resource\Context\Context;

/**
 * An ElementContext is a container that holds multiple predefined "Context" Object
 *
 * ElementContext for COLLECTION
 */
class ElementContext
{
    public readonly Context $fieldset;
    public readonly Context $title;
    public readonly Context $subtitle;
    public readonly Context $instruction;
    public readonly Context $container;

    public function __construct(protected Collection $collection)
    {
        $this->fieldset = new Context(
            UssElement::NODE_FIELDSET,
            new WrapperHandler($this)
        );

        $this->title = new Context(
            UssElement::NODE_LEGEND,
            new TitleHandler($this)
        );

        $this->subtitle = new Context(
            UssElement::NODE_P,
            new SubTitleHandler($this)
        );

        $this->instruction = new Context(
            UssElement::NODE_DIV,
            new InstructionHandler($this)
        );

        $this->container = new Context(
            UssElement::NODE_DIV,
            new ContainerHandler($this)
        );
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }
}
