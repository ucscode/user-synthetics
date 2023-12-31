<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Foundation\ElementContext;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Service\FieldUtils;

abstract class AbstractCollection implements CollectionInterface
{
    protected array $fields = [];
    protected ElementContext $elementContext;

    public function __construct()
    {
        $this->elementContext = new ElementContext($this);
    }

    protected function swapField(UssElement $fieldElement, ?UssElement $oldFieldElement): void
    {
        $collectionContainer = $this->elementContext->container->getElement();
        $oldFieldElement ?
            $collectionContainer->replaceChild($fieldElement, $oldFieldElement) :
            $collectionContainer->appendChild($fieldElement);
    }

    protected function welcomeField(string $name, Field $field): void
    {
        (new FieldUtils())->welcomeGadget($name, $field->getElementContext()->gadget);
    }
}
