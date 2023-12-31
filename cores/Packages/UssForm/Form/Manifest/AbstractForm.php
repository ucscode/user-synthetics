<?php

namespace Ucscode\UssForm\Form\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Form\Attribute;
use Ucscode\UssForm\Resource\Service\FieldUtils;

abstract class AbstractForm implements FormInterface
{
    public readonly UssElement $element;
    protected array $collections = [];

    public function __construct(protected Attribute $attribute = new Attribute())
    {
        $this->element = new UssElement(UssElement::NODE_FORM);
        $this->addCollection("default", new Collection());
    }

    protected function swapCollection(UssElement $collection, ?UssElement $oldCollection): void
    {
        $oldCollection ?
            $this->element->replaceChild($collection, $oldCollection) :
            $this->element->appendChild($collection);
    }

    protected function welcomeCollection(string $name, Collection $collection): void
    {
        $fieldsetContext = $collection->getElementContext()->fieldset;
        if(!$fieldsetContext->isFixed()) {
            $fieldsetContext->addClass((new FieldUtils())->simplifyContent($name, '-') . "-collection");
        }
    }
}
