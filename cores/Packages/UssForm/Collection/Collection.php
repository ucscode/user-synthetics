<?php

namespace Ucscode\UssForm\Collection;

use Ucscode\UssForm\Collection\Manifest\AbstractCollection;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Collection\Foundation\ElementContext;
use Ucscode\UssForm\Resource\Facade\Position;

class Collection extends AbstractCollection
{
    public function addField(string $name, Field $field): self
    {
        $oldField = $this->getField($name);
        $this->fields[$name] = $field;
        $this->swapField(
            $field->getElementContext()->frame->getElement(),
            $oldField?->getElementContext()->frame->getElement()
        );
        $this->welcomeField($name, $field);
        return $this;
    }

    public function getField(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    public function removeField(string|Field $context): ?Field
    {
        if($this->hasField($context)) {
            $field = $context instanceof Field ? $context : $this->getField($context);
            $name = $this->getFieldName($field);
            unset($this->fields[$name]);
            $fieldElement = $field->getElementContext()->frame->getElement();
            $fieldElement->getParentElement()->removeChild($fieldElement);
            return $field;
        }
        return null;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function hasField(string|Field $context): bool
    {
        return $context instanceof Field ?
            $this->getFieldName($context) !== false :
            !!$this->getField($context);
    }

    public function setFieldPosition(string|Field $field, Position $position, string|Field $targetField): bool
    {
        $field = $field instanceof Field ? $field : $this->getField($field);
        $targetField = $targetField instanceof Field ? $targetField : $this->getField($targetField);

        if($this->hasField($field) && $this->hasField($targetField)) {

            $fieldElement = $field->getElementContext()->frame->getElement();
            $targetElement = $targetField->getElementContext()->frame->getElement();
            $containerElement = $this->elementContext->container->getElement();

            $position === Position::BEFORE ?
                $containerElement->insertBefore($fieldElement, $targetElement) :
                $containerElement->insertAfter($fieldElement, $targetElement);

            return true;
        }

        return false;
    }

    public function getFieldName(Field $field): ?string
    {
        $name = array_search($field, $this->getFields(), true);
        return $name !== false ? $name : null;
    }

    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }
}
