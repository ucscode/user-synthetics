<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElementInterface;
use Ucscode\UssElement\UssElement;

class UssForm extends UssElement implements UssFormInterface
{
    public const INPUT = self::NODE_INPUT;
    public const SELECT = self::NODE_SELECT;
    public const TEXTAREA = self::NODE_TEXTAREA;
    public const BUTTON = self::NODE_BUTTON;

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

    private ?array $populate = [];
    private array $details = [];
    private string $radioKey = 'data-checked';

    /**
     * [PUBLIC] METHODS
     *
     * This methods can be called publicly
     *
     * @ignore
     */
    public function __construct(string $name, ?string $route = null, string $method = 'GET', string $enctype = null)
    {
        parent::__construct(self::NODE_FORM);
        $this->setAttribute('name', $name);
        $this->setAttribute('action', $route);
        $this->setAttribute('method', strtoupper($method));
        if(!empty($enctype)) {
            $this->setAttribute('enctype', $enctype);
        };
        $this->setElementId("_ussf_" . $name, null, $this);
    }

    /**
     * Provides an array of data for debugging purposes when this object is printed or var_dumped.
     *
     * @return array An array containing debug information about the UssForm object.
     */
    public function __debugInfo()
    {
        $info = parent::__debugInfo();
        $info['details'] = $this->details;
        return $info;
    }

    /**
     * Add Input Field
     *
     * @param string $name The name of the field
     *
     * @param string $fieldType The type of field; UssForm::INPUT, UssForm::SELECT, UssForm::TEXTAREA, UssForm::BUTTON"
     *
     * @param string|array|null $context
     * - UssForm::INPUT - Context is a string that defines the field type e.g UssForm::TYPE_TEXT, UssForm::TYPE_NUMBER...
     * - UssForm::SELECT - Context is an array that defines the field options
     * - UssForm::TEXTAREA - Context is not used
     * - UssForm::BUTTON - Context defines submit button of type UssForm::BUTTON or UssForm::INPUT
     *
     * @param array $data An array of configurations
     *
     * @return UssElement The added field element
     */
    public function add(
        string $name,
        string $fieldType,
        array|string|null $context = null,
        array $config = []
    ): UssElement {

        /**
         * Build Different Widget Base On Provided Field Type
         * Context In Different Area
         *
         * TEXTAREA - Context is Null
         * SELECT - Context is an array with choices
         * BUTTON - Context is either "button" or "input"
         * INPUT - Context is any input type such as "input, number, date, url..."
         */

        $fieldColumn = $this->buildFieldElements($name, $fieldType, $context, $config);

        $this->appendField($fieldColumn);
        
        return $fieldColumn;

    }

    /**
     * Add a new row to the form.
     *
     * New fields will be added to the last added row
     *
     * @param string $class The CSS class for the row.
     *
     * @return UssElement The added row element.
     */
    public function addRow(string $class = ''): UssElement
    {
        $row = new UssElement(self::NODE_DIV);
        $row->setAttribute('class', 'row ' . $class);
        $this->appendChild($row);
        return $row;
    }

    /**
     * Get a collection of elements that makes up a field.
     *
     * @param string $name The name of the field.
     *
     * @return array|null An array containing each elements within the field or null if not found.
     */
    public function getFieldset(string $name): ?array {
        $fieldset = [];
        $identity = $this->makeIdentity($name, 'column');
        $column = $this->find('#' . $identity);
        if(empty($column)) {
            return null;
        } else {
            $column = $column[0];
        }
        // column, group, label, widget, report
        $fieldset['column'] = $column;
        $fieldset['group'] = call_user_func(function() use($column) {
            $groupset = ['input-single', 'input-group', 'form-check'];
            foreach($groupset as $class) {
                $element = $column->find(".{$class}")[0] ?? null;
                if(!empty($element)) {
                    return $element;
                }
            }   
        });
        $fieldset['label'] = $column->find('label')[0] ?? null;
        $fieldset['report'] = $column->find('.form-report')[0] ?? null;
        $fieldset['widget'] = $column->find("[name], input[type], select, textarea, button[type], button")[0] ?? null;
        return $fieldset;
    }

    /**
     * Populate the form with data
     *
     * Automatically set the value of field widgets with populated data;
     * Values set directly on the form will override the populated data
     *
     * @param array $data An associative array of data to populate the form.
     *
     * @return void
     */
    public function populate(array $data): void
    {
        $result = $this->flattenArray($data);
        $this->populate = $result;
    }

    /**
     * Get the value of a form element.
     *
     * @param UssElement $node The form element.
     *
     * @return string|null The value of the form element or null.
     */
    public function getValue(UssElement $node): ?string
    {
        if(in_array($node->tagName, [self::INPUT, self::BUTTON], true)) {

            # Get input or button value
            $value = $node->getAttribute('value');
            
            if($this->isCheckable($node)) {

                return $node->getAttribute($this->radioKey);
                
            } else {

                return $value;

            }

        } elseif($node->tagName === self::SELECT) {

            # Get select value
            $children = $node->find('[selected]');

            if(!empty($children)) {
                return $children[0]->getAttribute('value');
            };

        } elseif($node->tagName === self::TEXTAREA) {

            # Get textarea value
            return $node->getContent();

        }

        return null;
    }

    /**
     * Set the value of a form element.
     *
     * @param UssElement $node      The form element.
     * @param mixed      $value     The value to set.
     * @param bool       $overwrite Whether to overwrite existing value.
     *
     * @return bool True if the value was set, false otherwise.
     */
    public function setValue(UssElement $node, $value, bool $overwrite = true): bool
    {
        if(is_null($value)) {
            $value = false;
        };

        if(is_scalar($value)) {
            
            $nodevalue = $this->getValue($node);
            $hasValue =  !is_null($nodevalue) && $nodevalue !== '';

            if(in_array($node->tagName, [self::INPUT, self::BUTTON], true)) {

                if(!$hasValue || $overwrite) {

                    if($this->isCheckable($node)) {

                        if(!empty($value)) {

                            $node->setAttribute('checked', 'checked');
                            $node->setAttribute($this->radioKey, '1');

                        } else {

                            $node->removeAttribute('checked');
                            $node->setAttribute($this->radioKey, '0');

                        }

                    } else {

                        $node->setAttribute('value', $value);

                    };

                    return true;

                };

            } elseif($node->tagName === self::SELECT) {

                if(!$hasValue || $overwrite) {
                    foreach($node->find("option") as $option) {
                        if($value == $option->getAttribute('value')) {
                            $option->setAttribute('selected', 'selected');
                            return true;
                        }
                    };
                }

            } elseif($node->tagName === self::TEXTAREA) {

                # Get Text area value;
                $hasValue = !empty($node->getContent());

                # Set the value if empty or forced
                if(!$hasValue || $overwrite) {
                    $node->setContent($value);
                    return true;
                };

            }

        };
        return false;
    }

    /**
     * Append a field to the active form row.
     *
     * @param UssElement $column The field to append.
     *
     * @param int $rowIndex The index of an existing row
     *
     * @return UssElement The row to which the field element was appended or null if not appended.
     */
    public function appendField(UssElement $column, ?int $rowIndex = null): ?UssElement
    {
        if(empty($this->children)) {
            $row = $this->addRow();
        } else if($rowIndex !== null) {
            $row = $this->getChild(abs($rowIndex));
        } else {
            $row = $this->lastChild();
            if($row->tagName !== self::NODE_DIV || !$row->hasAttributeValue('class', 'row')) {
                $row = $this->addRow();
            }
        };
        if(!empty($row)) {
            $row->appendChild($column);
        };
        return $row;
    }

    /**
     * Add a detail to the UssForm object that will not be rendered with the form.
     *
     * @param string $key   The key for the detail.
     * @param mixed  $value The value to associate with the key.
     *
     * @return bool True if the detail was added successfully, false otherwise.
     */
    public function addDetail(string $key, $value): bool
    {
        $this->details[$key] = $value;
        return isset($this->details[$key]);
    }

    /**
     * Get the value of a detail associated with the specified key.
     *
     * @param string $key The key of the detail to retrieve.
     *
     * @return mixed|null The value of the detail if found, or null if the key does not exist.
     */
    public function getDetail(string $key): mixed
    {
        return $this->details[$key] ?? null;
    }
    
    /**
     * Remove a detail with the specified key from the UssForm object.
     *
     * @param string $key The key of the detail to remove.
     *
     * @return void
     */
    public function removeDetail(string $key): void
    {
        if(isset($this->details[$key])) {
            unset($this->details[$key]);
        };
    }

    /**
     * Get HTML Output
     */
    public function getHTML(bool $indent = false): string
    {
        foreach($this->populate as $key => $value) {
            if(!is_scalar($value)) {
                continue;
            };
            $nodes = $this->find("[name='{$key}']");
            if(!empty($nodes)) {
                $node = $nodes[0];
                $isWidget = in_array($node->tagName, [
                    self::BUTTON,
                    self::INPUT,
                    self::TEXTAREA,
                    self::SELECT
                ]);
                if($isWidget) {
                    $this->setValue($node, $value, false);
                }
            };
        };
        return parent::getHTML($indent);
    }

    /**
     * Force Form element to never be void
     * @ignore
     */
    public function isVoid(bool $void): self
    {
        $this->void = false;
        return $this;
    }


    /**
     * [PROTECTED] METHODS
     *
     * This methods can be extended but cannot be called publicly
     *
     * @ignore
     */
    
    protected function buildButtonWidget(string $name, string $type, $data): UssElement
    {
        if($type !== self::INPUT) {
            $button = new UssElement(self::NODE_BUTTON);
        } else {
            $button = new UssElement(self::NODE_INPUT);
            $button->setAttribute('name', $name);
        };
        $button->setAttribute('type', self::TYPE_SUBMIT);
        $button->setAttribute('class', $data['class'] ?? 'btn btn-primary');
        if(array_key_exists('value', $data)) {
            $this->setValue($button, $data['value']);
        }
        if($button->hasAttribute('value') || !empty($data['use_name'])) {
            $button->setAttribute('name', $name);
        };
        $button->setContent($data['content'] ?? "Submit");
        return $button;
    }

    protected function buildInputWidget(string $name, string $type, array $data): UssElement
    {
        $input = new UssElement(self::NODE_INPUT);
        $input->setAttribute('name', $name);
        if($this->isCheckable($type)) {
            if($type === self::TYPE_SWITCH) {
                $type = self::TYPE_CHECKBOX;
                $input->setAttribute('role', self::TYPE_SWITCH);
            };
            $input->setAttribute('type', $type);
            if(array_key_exists('checked', $data)) {
                $this->setValue($input, $data['checked']);
            }
            $input->setAttribute('class', 'form-check-input ' . ($data['class'] ?? ''));
            if(!empty($data['value'])) {
                $input->setAttribute('value', $data['value']);
            }   
        } else {
            $input->setAttribute('type', $type);
            $input->setAttribute('class', 'form-control ' . ($data['class'] ?? ''));
            if(array_key_exists('value', $data)) {
                $this->setValue($input, $data['value']);
            }
        };
        return $input;
    }

    protected function buildSelectWidget(string $name, array $options, array $data): UssElement
    {
        $select = new UssElement(self::NODE_SELECT);
        $select->setAttribute('name', $name);
        $select->setAttribute('class', 'form-select ' . ($data['class'] ?? ''));
        foreach($options as $value => $display) {
            $option = new UssElement(self::NODE_OPTION);
            $option->setAttribute('value', $value);
            $option->setContent($display);
            $select->appendChild($option);
        };
        if(array_key_exists('value', $data)) {
            $this->setValue($select, $data['value']);
        }
        return $select;
    }

    protected function buildTextareaWidget(string $name, ?string $type, array $data): UssElement
    {
        $textarea = new UssElement(self::NODE_TEXTAREA);
        $textarea->setAttribute('name', $name);
        $textarea->setAttribute('class', 'form-control ' . ($data['class'] ?? ''));
        if(array_key_exists('value', $data)) {
            $this->setValue($textarea, $data['value']);
        }
        return $textarea;
    }

    protected function buildField(string $name, UssElementInterface $widget, array $data): UssElement
    {
        $label = $data['label'] ?? call_user_func(function () use ($name) {
            preg_match_all("/[a-z0-9_\-]+/i", $name, $matches);
            $label = array_map(function ($value) {
                $value = str_replace("_", " ", $value);
                return ucwords(trim($value));
            }, ($matches[0] ?? []));
            return implode(" ", $label);
        });
        
        $field = [
            'widget' => $widget,
            'column' => (new UssElement(self::NODE_DIV))->setAttribute('class', $data['column'] ?? 'col-md-12 mb-3'),
        ];

        if($this->isCheckable($widget)) {
            $field = $this->constructCheckableField($field, $name, $label, $data);
        } else {
            $field = $this->constructRegularField($field, $name, $label, $data);
        };
        
        return $this->concludeField($field, $name, $data);

    }

    protected function &constructRegularField(array &$field, string $name, string $label, array $data): array {

        $field['group'] = (new UssElement(self::NODE_DIV))->setAttribute('class', 'input-single');

        $field['label'] = (new UssElement(self::NODE_LABEL))
            ->setAttribute('class', $data['label_class'] ?? 'form-label')
            ->setContent($label);
        
        $field['report'] = (new UssElement(self::NODE_DIV))->setAttribute('class', 'form-text form-report');

        # Update Data

        $this->updateReport($field['report'], $data['report'] ?? null);

        # Create Formation
        $field['column']->appendChild($field['label']);
        $field['column']->appendChild($field['group']);
        $field['column']->appendChild($field['report']);

        $field['group']->appendChild($field['widget']);

        if(isset($data['group'])) {
            $this->buildFieldGroup($field['group'], $data['group']);
        };

        return $field;

    }

    protected function &constructCheckableField(array &$field, string $name, string $label, array $data): array
    {   
        # Get Type
        $type = $field['widget']->getAttribute('role');

        $groupclass = 'form-check';
        if($type === self::TYPE_SWITCH) {
            $groupclass .= ' form-switch';
        };

        $field['group'] = (new UssElement(self::NODE_DIV))->setAttribute('class', $groupclass);

        $field['label'] = (new UssElement(self::NODE_LABEL))
            ->setAttribute('class', 'form-check-label')
            ->setContent($label);

        $field['column']->appendChild($field['group']);
        $field['group']->appendChild($field['widget']);
        $field['group']->appendChild($field['label']);
        
        return $field;
    }

    protected function buildButtonField(string $name, UssElement $widget, array $data): UssElement
    {

        $field = [
            'column' => (new UssElement(self::NODE_DIV))->setAttribute('class', $data['column'] ?? 'col-md-12 mb-3'),
            'widget' => $widget
        ];

        # Create Formation
        $field['column']->appendChild($field['widget']);

        return $this->concludeField($field, $name, $data);
    }

    protected function buildHiddenField(string $name, UssElement $widget, array $data): UssElement
    {
        $field = ['widget' => $widget];
        return $this->concludeField($field, $name, $data, 'widget');
    }

    protected function makeIdentity(string $name, ?string $part): string {
        
        $formId = $this->getAttribute('id');

        if(!is_null($formId)) {
            $prefix = $formId . "_{$name}_";
        } else {
            $prefix = $name;
        };

        $identity = $prefix . $part;

        $identity = str_replace(
            array("[", "]", ' '),
            array("-", "", '_'),
            $identity
        );

        preg_match_all("/[a-z0-9_\-]+/i", $identity, $match);

        if(!empty($match[0])) {
            $match[0] = array_map(function ($value) {
                return preg_replace("/^_/", '', $value);
            }, $match[0]);
            $context = array_filter($match[0]);
        } else {
            $context = '';
        }

        $identity = "_" . implode("_", $context);

        return $identity;
    }


    /**
     * [PRIVATE] METHODS
     * 
     * This methods cannot be extended
     *
     * @ignore
     */

    private function buildFieldElements(string $name,string $fieldType, array|string|null $context, array $config): UssElement {

        if($fieldType === self::TEXTAREA) {
            $widget = $this->buildTextareaWidget($name, $context, $config);
        } elseif($fieldType === self::SELECT) {
            $widget = $this->buildSelectWidget($name, $context ?? [], $config);
        } elseif($fieldType === self::BUTTON) {
            $widget = $this->buildButtonWidget($name, $context ?? self::BUTTON, $config);
        } else {
            $widget = $this->buildInputWidget($name, $context ?? self::TYPE_TEXT, $config);
        }

        # General Config for all widgets
        $widget = $this->configureWidget($widget, $config);

        # Using the created widgets, build the form field

        if($widget->hasAttributeValue('type', self::TYPE_SUBMIT) && in_array($widget->tagName, [self::BUTTON, self::INPUT], true)) {
            # TYPE_SUBMIT
            $fieldColumn = $this->buildButtonField($name, $widget, $config);

        } elseif($widget->hasAttributeValue('type', self::TYPE_HIDDEN) && $widget->tagName === self::INPUT) {
            # TYPE_HIDDEN
            $fieldColumn = $this->buildHiddenField($name, $widget, $config);

        } else {
            # OTHER TYPES
            $fieldColumn = $this->buildField($name, $widget, $config);
        }

        return $fieldColumn;

    }

    private function setElementId(string $name, ?string $part, UssElement $node): void
    {
        if($node->hasAttribute('id')) {
            return;
        };

        $identity = $this->makeIdentity($name, $part);

        $node->setAttribute("id", $identity);

    }

    private function buildFieldGroup(UssElement $node, $data): void
    {
        if(!is_array($data)) {
            $data = ['append' => $data];
        };
        
        # Available Group Options
        $default = ['prepend', 'append'];
        
        # Parse Data
        foreach($default as $index) {
            $value = $data[$index] ?? null;
            if(is_null($value)) {
                continue;
            } elseif(!is_scalar($value) && !($value instanceof UssElement)) {
                continue;
            } else {
                if(is_scalar($value)) {
                    $element = (new UssElement(self::NODE_DIV))->setAttribute('class', 'input-group-text');
                    $element->setContent($value);
                } else {
                    $element = $value;
                    $autoFillButton = [
                        'type' => self::BUTTON,
                        'class' => 'btn btn-outline-secondary'
                    ];
                    foreach($autoFillButton as $key => $attrValue) {
                        if($element->tagName === self::BUTTON && !$element->hasAttribute($key)) {
                            $element->setAttribute($key, $attrValue);
                        }
                    };
                };
                $method = $index . "Child"; // appendChild or prependChild
                $node->{$method}($element);
            }
        };

        if(count($node->children) > 1) {
            $node->setAttribute('class', 'input-group');
        };

    }

    private function updateReport(UssElement $node, $data): void
    {
        if(is_null($data)) {
            return;
        } elseif(!is_array($data) && !is_scalar($data)) {
            return;
        } else {
            if(is_scalar($data)) {
                // convert to array;
                $data = ['message' => $data];
            };
            if(!is_scalar($data['message']) || is_bool($data['message'])) {
                return;
            } else {
                $node->setContent($data['message']);
                if(!empty($data['class']) && is_scalar($data['class'])) {
                    $node->addAttributeValue('class', $data['class']);
                };
            };
        };
    }

    private function configureWidget(UssElement $widget, array $data): UssElement
    {
        // Create Custom ID
        if(!empty($data['id'])) {
            if(preg_match("/\w(?:[a-z0-9_\-]+)?/", $data['id'])) {
                $widget->setAttribute('id', $data['id']);
            };
        };
        // Set Field As Required
        if(!empty($data['required'])) {
            $widget->setAttribute('required', 'required');
        }
        // Set Widget Attributes
        if(!empty($data['attr']) && is_array($data['attr'])) {
            foreach($data['attr'] as $key => $value) {
                if(in_array($key, ['type'])) {
                    continue;
                }
                if(is_numeric($key)) {
                    $key = $value;
                }
                $widget->setAttribute($key, $value);
            };
        }
        // Ignore the name attribute
        if(!empty($data['ignore'])) {
            $widget->removeAttribute('name');
        };
        // return the widget
        return $widget;
    }

    private function isCheckable(string|UssElement $entity): bool {
        if(!is_string($entity)) {
            if($entity->tagName !== self::INPUT) {
                return false;
            }
            $value = $entity->getAttribute('type');
        } else $value = $entity;
        return in_array($value, [self::TYPE_CHECKBOX, self::TYPE_SWITCH, self::TYPE_RADIO]);
    }

    private function &concludeField(array &$field, string &$name, array &$data, string $__key = 'column'): UssElement
    {
        # Dedicate Identity
        $selectives = [
            'column',
            'widget',
            'report'
        ];

        foreach($selectives as $part) {
            if(!empty($field[$part])) {
                $element = $field[$part];
                $this->setElementId($name, $part, $element);
                if($part === 'widget' && $this->isCheckable($element)) {
                    if(!empty($field['label'])) {
                        $field['label']->setAttribute('for', $element->getAttribute('id'));
                    }
                }
            };
        };

        if(is_callable($data['fields'] ?? null)) {
            call_user_func($data['fields'], $field);
        };

        return $field[$__key];
    }

    /**
     * Recursively flattens a multi-dimensional array and constructs keys in the specified format.
     *
     * @param array  $array   The multi-dimensional array to flatten.
     * @param string $prefix  (Optional) The prefix to prepend to keys.
     *
     * @return array The flattened array with keys in the specified format.
     */
    private function flattenArray($value, ?string $key = null) {
        $result = [];
        if (!is_array($value)) {
            // If the value is not an array, assign it to the result with the specified key
            $result[$key] = $value;
        } else {
            // If the value is an array, iterate through its elements
            foreach ($value as $innerKey => $innerValue) {
                if (func_num_args() > 1) {
                    // If a key is provided, concatenate it with the inner key using square brackets
                    $newKey = $key . "[$innerKey]";
                } else {
                    // If no key is provided, use the inner key directly
                    $newKey = $innerKey;
                }
                // Recursively call flattenArray with the inner value and new key
                $result = array_merge($result, $this->flattenArray($innerValue, $newKey));
            }
        }
        return $result;
    }

}
