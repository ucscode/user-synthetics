<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Resource\Context\Context;
use Ucscode\UssForm\Resource\Context\AbstractContext;

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

    public function __construct()
    {
        $this->buildWrapperContext();
        $this->buildTitleContext();
        $this->buildSubTitleContext();
        $this->buildInstructionContext();
        $this->buildContainerContext();
    }

    protected function buildWrapperContext(): void
    {
        $this->fieldset = new Context(
            UssElement::NODE_FIELDSET,
            new class ($this) extends AbstractContext {
                public function initialize(UssElement $element): void
                {
                    $element->setAttribute('class', 'collection wrapper col-12');
                }
            }
        );
    }

    protected function buildTitleContext(): void
    {
        $this->title = new Context(
            UssElement::NODE_LEGEND,
            new class () extends AbstractContext {
                public function initialize(UssElement $element): void
                {
                    $element->setAttribute('class', 'title');
                }
            }
        );
    }

    protected function buildSubTitleContext(): void
    {
        $this->subtitle = new Context(
            UssElement::NODE_P,
            new class () extends AbstractContext {
                public function initialize(UssElement $element): void
                {
                    $element->setAttribute('class', 'subtitle small');
                }
            }
        );
    }

    protected function buildInstructionContext(): void
    {
        $this->instruction = new Context(
            UssElement::NODE_DIV,
            new class () extends AbstractContext {
                public function initialize(UssElement $element): void
                {
                    $element->setAttribute('class', 'instruction alert alert-info');
                }
            }
        );
    }

    protected function buildContainerContext(): void
    {
        $this->container = new Context(
            UssElement::NODE_DIV,
            new class () extends AbstractContext {
                public function initialize(UssElement $element): void
                {
                    $element->setAttribute('class', 'row container');
                }
            }
        );
    }
}
