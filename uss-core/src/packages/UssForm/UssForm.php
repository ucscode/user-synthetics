<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\UssFormInterface;
use Ucscode\UssForm\Abstraction\AbstractUssForm;
use Ucscode\UssForm\Internal\UssFormFieldStack;

class UssForm extends AbstractUssForm 
{
    /**
     * The fieldstack becomes the current active stack to hold the next field
     * 
     * @param string $name (optional): The name of the fieldstack; Auto-generated if not given
     * 
     * @return UssFormFieldStack: The system generated fieldstack instance
     */
    public function addFieldStack(?string $name = null, bool $isDiv = false): UssFormFieldStack
    {
        $name = $name ?? 'stack' . (++self::$stackIndex);
        $fieldStack = $this->getFieldStack($name);
        if(!$fieldStack){
            $fieldStack = new UssFormFieldStack($name, $isDiv, $this);
            $this->fieldStacks[$name] = $fieldStack;
            $this->stackContainer->appendChild($fieldStack->getFieldStackAsElement());
        }
        return $fieldStack;
    }

    /**
     * Get a fieldstack by name
     * 
     * @return ?UssFormFieldStack: The fieldstack instance or null of not found
     */
    public function getFieldStack(string $name): ?UssFormFieldStack
    {
        return $this->fieldStacks[$name] ?? null;
    }

    /**
     * Get a fieldstack by fieldname
     */
     public function getFieldStackByField(string $name): ?UssFormFieldStack
    {
        foreach($this->fieldStacks as $fieldStack) {
            if($fieldStack->getField($name)) {
                return $fieldStack;
            }
        }
        return null;
    }

    /**
     * Remove a fieldstack form the form 
     * 
     * @param string $name: The name of the field stack to remove
     * 
     * @return self: The UssForm instances
     */
    public function removeFieldStack(string $name): self
    {
        $fieldStack = $this->getFieldStack($name);
        if($fieldStack) {
            unset($this->fieldStacks[$name]);
            $element = $fieldStack->getFieldStackAsElement();
            $parent = $element->getParentElement();
            if($parent) {
                $parent->removeChild($element);
            }
        }
        return $this;
    }

    /**
     * Get all available fieldstacks
     * 
     * @return array: A list of fieldstacks
     */
    public function getFieldStacks(): array
    {
        return $this->fieldStacks;
    }

    /**
     * Add a field to the most active fieldstack in the form instance
     * 
     * @param string $name: The name of the field
     * @param UssFormField $field: The instance of the field
     * @param array $options: Options for additional configuration of the field
     * 
     * @return self: The UssForm instance
     */
    public function addField(string $name, UssFormField $field, array $options = []): self
    {
        $fieldStack = $this->getActiveFieldStack($options['fieldStack'] ?? null, $name);
        $this->alterField($name, $field, $options);
        $fieldStack->addField($name, $field);
        return $this;
    }

    /**
     * Find a field from all available fieldstack
     * 
     * @param string $name: The name of the field to get
     * 
     * @return ?UssFormField: The field instance or null
     */
    public function getField(string $name): ?UssFormField
    {
        $field = null;
        foreach($this->fieldStacks as $fieldStack) {
            if($field = $fieldStack->getField($name)) {
                break;
            }
        };
        return $field;
    }

    /**
     * Remove a field from a stack if found
     */
    public function removeField(string $name): self
    {
        foreach($this->fieldStacks as $fieldStack) {
            $fieldStack->removeField($name);
        }
        return $this;
    }

    /**
     * @method getFields
     */
    public function getFields(): array
    {
        $availableFields = [];
        foreach($this->fieldStacks as $fieldStack) {
            $fields = $fieldStack->getFields();
            $availableFields = array_merge($availableFields, $fields);
        }
        return $availableFields;
    }

    /**
     * @method addCustomElement
     */
    public function addCustomElement(string $name, UssElement $element, array $options = []): UssFormInterface
    {
        $fieldStack = $this->getActiveFieldStack($options['fieldStack'] ?? null);
        $fieldStack->addElement($name, $element);
        $this->elements[$name] = $element;
        return $this;
    }

    /**
     * @method getCustomElement
     */
    public function getCustomElement(string $name): ?UssElement
    {
        return $this->elements[$name] ?? null;
    }

    /**
     * @method removeCustomElement
     */
    public function removeCustomElement(string $name): self
    {
        return $this;
    }

    /**
     * @method populate
     */
    public function populate(array $data): UssFormInterface
    {
        $this->flatArray = $this->flattenArray($data);
        return $this;
    }

    /**
     * @method buildNodes
     */
    protected function buildNode(UssElement $node, ?int $indent)
    {
        if($this->flatArray) {
            foreach($this->flatArray as $name => $value) {
                $field = call_user_func(function () use ($name, $value): ?UssFormField {
                    $fields = [];
                    foreach($this->getFields() as $field) {
                        if($field->getWidgetAttribute('name') === $name) {
                            $fields[] = $field;
                        }
                    }
                    if(!empty($fields)) {
                        if(count($fields) < 2) {
                            return $fields[0];
                        } else {
                            foreach($fields as $field) {
                                if($field->getWidgetValue() == $value) {
                                    return $field;
                                }
                            }
                        }
                    }
                    return null;
                });
                if($field) {
                    if($field->isCheckable()) {
                        $field->setWidgetChecked($field->getWidgetValue() == $value);
                    } else {
                        $field->setWidgetValue($value);
                    }
                }
            }
        }

        return parent::buildNode($node, $indent);
    }
}
