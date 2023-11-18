<?php

namespace Ucscode\UssForm\Abstraction;

use Ucscode\UssElement\UssElement;
use Ucscode\UssElement\UssElementNodeListInterface;
use Ucscode\UssForm\Interface\UssFormInterface;
use Ucscode\UssForm\UssFormField;
use Ucscode\UssForm\Internal\UssFormFieldStack;

abstract class AbstractUssForm extends UssElement implements UssFormInterface, UssElementNodeListInterface
{
    public readonly UssElement $stackContainer;
    protected array $fieldStacks = [];
    protected array $elements = [];
    protected ?array $flatArray = null;
    protected static $stackIndex = 0;

    /**
     * @method __construct
     */
    public function __construct(string $name, ?string $action = null, string $method = 'GET', ?string $enctype = null)
    {
        parent::__construct(UssElement::NODE_FORM);
        $this->setDefaultAttributes($name, $action, $method, $enctype);
        $this->stackContainer = new UssElement(UssElement::NODE_DIV);
        $this->stackContainer->setAttribute('class', 'stack-container row');
        $this->appendChild($this->stackContainer);
    }
        
    /**
     * @method setDefaultAttributes
     */
    protected function setDefaultAttributes(string $name, ?string $action, string $method, ?string $enctype): void
    {
        $this->setAttribute('name', $name);
        $this->setAttribute('action', $action);
        $this->setAttribute('method', strtoupper($method));
        if(!is_null($enctype)) {
            $this->setAttribute('enctype', $enctype);
        };
        $this->setAttribute('id', "ussf-" . $name);
    }

    /**
     * @method getAssignedFieldStack
     */
    protected function getActiveFieldStack(?string $fieldStackName, string $name): UssFormFieldStack
    {
        $fieldStack = $this->getFieldStackByField($name) ?? $this->getFieldStack($fieldStackName ?? '');
        if(!$fieldStack) {
            if(empty($this->fieldStacks)) {
                $fieldStackName = 'default';
                $fieldStack = $this->addFieldStack($fieldStackName, true);
            } else {
                $fieldStack = end($this->fieldStacks);
            }
        };
        return $fieldStack;
    }

    /**
     * Recursively flattens a multi-dimensional array and constructs keys in the specified format.
     *
     * @param array  $array   The multi-dimensional array to flatten.
     * @param string $prefix  (Optional) The prefix to prepend to keys.
     * @return array The flattened array with keys in the specified format.
     */
    protected function flattenArray($value, ?string $key = null)
    {
        $result = [];
        if (!is_array($value)) {
            $result[$key] = $value;
        } else {
            foreach ($value as $innerKey => $innerValue) {
                if (func_num_args() > 1) {
                    $newKey = $key . "[$innerKey]";
                } else {
                    $newKey = $innerKey;
                }
                $result = array_merge($result, $this->flattenArray($innerValue, $newKey));
            }
        }
        return $result;
    }

    /**
     * @method labelize
     */
    protected function labelize(string $label): string
    {
        $entity = ['[', ']', '_'];
        $with = ['', '', ' '];
        return ucfirst(str_replace($entity, $with, $label));
    }

    /**
     * @method configureAddedField
     */
    protected function alterField(string $name, UssFormField $field, array $options): void
    {
        $field->setLabelValue($field->getLabelValue() ?? $this->labelize($name));
        if(($options['mapped'] ?? null) !== false) {
            $field->setWidgetAttribute('name', $name);
        }
    }
}