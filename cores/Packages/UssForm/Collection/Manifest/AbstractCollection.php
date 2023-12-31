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

    protected function swapField(
        UssElement $element, 
        ?UssElement $oldElement, 
        ?UssElement $oldLineBreak
    ): void
    {
        $collectionContainer = $this->elementContext->container->getElement();
        $collectionContainer->appendChild($element);
        if($oldElement) {
            $collectionContainer->replaceChild($element, $oldElement);
            $oldLineBreak->hasParentElement() ? $oldLineBreak->getParentElement()->removeChild($oldLineBreak) : null;
        }
    }

    protected function welcomeField(string $name, Field $field): void
    {
        $this->anchorLineBreak($field);
        $fieldUtils = new FieldUtils();
        $context = $field->getElementContext();
        $simplifiedName = $fieldUtils->simplifyContent($name, '-');
        $fieldUtils->welcomeGadget($name, $context->gadget);
        if(!$context->frame->isFixed()) {
            $context->frame->addClass($simplifiedName . "-field");
            $context->lineBreak->addClass($simplifiedName . "-linebreak");
        }
    }

    protected function anchorLineBreak(Field|array $fields, bool $embed = true): bool
    {
        $collectionContainer = $this->elementContext->container->getElement();
        !is_array($fields) ? $fields = [$fields] : null;
        foreach($fields as $field) {
            if($field instanceof Field) {
                $context = $field->getElementContext();
                $lineBreakElement = $context->lineBreak->getElement();
                $embed ?
                    $collectionContainer->insertAfter($lineBreakElement, $context->frame->getElement()) :
                    ($lineBreakElement->hasParentElement() ? 
                        $lineBreakElement->getParentElement()->removeChild($lineBreakElement) : 
                        null
                    );
            }
        }
        return true;
    }
}
