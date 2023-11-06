<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssElement\UssElementNodeListInterface;

class UssForm extends UssElement implements UssFormInterface, UssElementNodeListInterface
{
    public const TYPE_TEXT = 'text';
    public const TYPE_NUMBER = 'number';
    public const TYPE_DATE = 'date';
    public const TYPE_TIME = 'time';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_RADIO = 'radio';
    public const TYPE_SWITCH = 'switch';
    public const TYPE_FILE = 'file';
    public const TYPE_COLOR = 'color';
    public const TYPE_RANGE = 'range';
    public const TYPE_SEARCH = 'search';
    public const TYPE_URL = 'url';
    public const TYPE_TEL = 'tel';
    public const TYPE_HIDDEN = 'hidden';
    public const TYPE_SUBMIT = 'submit';
    public const TYPE_BUTTON = 'button';
    public const TYPE_RESET = 'reset';
    public const TYPE_DATETIME_LOCAL = 'datetime-local';

    protected array $fieldStacks = [];
    protected array $fields = [];
    protected array $elements = [];
    protected ?array $flatArray = null;

    /**
     * @method __construct
     */
    public function __construct(string $name, ?string $action = null, string $method = 'GET', ?string $enctype = null)
    {
        parent::__construct(UssElement::NODE_FORM);
        $this->setDefaultAttributes($name, $action, $method, $enctype);
    }

    /**
     * @method addField
     */
    public function addField(string $name, UssFormField $field, ?string $fieldStackName = null): UssFormInterface
    {
        $fieldStack = $this->getActiveFieldStack($fieldStackName);
        if(is_null($field->getLabelValue())) {
            $field->setLabelValue($this->labelize($name));
        };
        if(is_null($field->getWidgetAttribute('name'))) {
            $field->setWidgetAttribute('name', $name);
        }
        $fieldStack->addField($name, $field);
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
     * @method addCustomElement
     */
    public function addCustomElement(string $name, UssElement $element, ?string $fieldStackName = null): UssFormInterface
    {
        $fieldStack = $this->getActiveFieldStack($fieldStackName);
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
     * @method addFieldStack
     */
    public function addFieldStack(string $name, ?UssFormFieldStack $fieldStack = null): UssFormInterface
    {
        $this->fieldStacks[$name] = $fieldStack;
        $this->appendChild($fieldStack->getFieldStackAsElement());
        return $this;
    }

    /**
     * @method getFieldStack
     */
    public function getFieldStack(string $name): ?UssFormFieldStack
    {
        return $this->fieldStacks[$name] ?? null;
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
                $field = call_user_func(function() use($name, $value): ?UssFormField {
                    $fields = [];
                    foreach($this->fields as $field) {
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
    protected function getActiveFieldStack(?string $fieldStackName): UssFormFieldStack
    {
        $fieldStack = $this->getFieldStack($fieldStackName ?? '');
        if(!$fieldStack) {
            if(empty($this->fieldStacks)) {
                $fieldStackName = 'default';
                $fieldStack = new UssFormFieldStack($fieldStackName);
                $this->addFieldStack($fieldStackName, $fieldStack);
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
    private function flattenArray($value, ?string $key = null)
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
}
