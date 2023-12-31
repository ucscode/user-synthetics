<?php

namespace Ucscode\UssForm\Form\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Form\Attribute;

abstract class AbstractForm implements FormInterface
{
    public readonly UssElement $element;
    protected array $collections = [];

    public function __construct(protected Attribute $attribute = new Attribute())
    {
        $this->element = new UssElement(UssElement::NODE_FORM);
        $this->addCollection("default", new Collection());
    }

    protected function swapCollection(UssElement $collection): void
    {
        $this->element->appendChild($collection);
    }
}
