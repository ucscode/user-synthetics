<?php

namespace Ucscode\UssForm\Trait\Fieldstack;

use Ucscode\UssForm\UssFormField;

trait FieldstackFieldTrait
{
    protected array $fields = [];

    /**
     * @method addField
     */
    public function addField(string $name, UssFormField $field): self
    {
        //Get last field that was added with the same name
        $previousField = $this->getField($name);

        if(!$previousField) {
            // Add element to inner container
            $this->innerContainer['element']->appendChild(
                $field->getFieldAsElement()
            );
        } else {
            if($previousField !== $field) {
                // Avoid unwanted linebreak elements
                if($previousField->hasLineBreak()) {
                    $previousField->getLineBreak()
                        ->getParentElement()
                        ->removeChild($previousField->getLineBreak());
                }
                // Replace previous element with new element
                $this->innerContainer['element']->replaceChild(
                    $field->getFieldAsElement(),
                    $previousField->getFieldAsElement()
                );
            }
        }

        // Insert the current field line break if any
        if($field->hasLineBreak()) {
            $this->innerContainer['element']->insertAfter(
                $field->getLineBreak(),
                $field->getFieldAsElement()
            );
        };

        // Save the field instance
        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * @method getField
     */
    public function getField(string $name): ?UssFormField
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * @method getFields
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @method removeField
     */
    public function removeField(string $name): self
    {
        // Get the field instance
        $field = $this->getField($name);

        if($field) {
            // Free up the field instance
            unset($this->fields[$name]);

            // Get the relative elements
            $element = $field->getFieldAsElement();
            $lineBreak = $field->getLineBreak();

            // Remove the field element
            if($element->getParentElement() === $this->innerContainer['element']) {
                $this->innerContainer['element']->removeChild($element);
                if($lineBreak) {
                    $lineBreak->getParentElement()->removeChild($lineBreak);
                }
            }
        }
        return $this;
    }
}
