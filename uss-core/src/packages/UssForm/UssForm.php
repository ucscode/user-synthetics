<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElementInterface;
use Ucscode\UssElement\UssElement;

class UssForm extends UssElement implements UssFormInterface
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

    private ?array $populate = [];
    private array $details = [];
    private string $radioKey = 'data-checked';

    /**
     * @method __construct
     */
    public function __construct(string $name, ?string $action = null, string $method = 'GET', string $enctype = null)
    {
        parent::__construct(parent::NODE_FORM);

        $this->setAttribute('name', $name);
        $this->setAttribute('action', $action);
        $this->setAttribute('method', strtoupper($method));

        if(!empty($enctype)) {
            $this->setAttribute('enctype', $enctype);
        };

        $this->setElementId("_ussf_" . $name, null, $this);
    }

    /**
     * @method __debugInfo
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
     * @param string $nodeName: UssForm::NODE_INPUT, UssForm::NODE_SELECT, UssForm::NODE_TEXTAREA, UssForm::NODE_BUTTON"
     *
     * @param string|array|null $context
     * - UssForm::NODE_INPUT - Defines the field type E.G UssForm::TYPE_TEXT, UssForm::TYPE_NUMBER...
     * - UssForm::NODE_SELECT - Defines an array of field options
     * - UssForm::NODE_TEXTAREA - Null
     * - UssForm::NODE_BUTTON - Defines button type E.G UssForm::TYPE_BUTTON or UssForm::TYPE_SUBMIT
     *
     * @param array $data An array of configurations
     *
     * @return UssElement The added field element
     */
    public function add(string $name, string $nodeName, array|string|null $context = null, array $config = []): UssElement
    {
        $fieldSet = $this->buildFieldElements($name, $nodeName, $context, $config);
        $this->appendField($fieldSet);
        return $fieldSet;
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
    public function getFieldset(string $name): ?array
    {
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
        $fieldset['group'] = call_user_func(function () use ($column) {
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
        if(in_array($node->tagName, [self::NODE_INPUT, self::NODE_BUTTON], true)) {

            # Get input or button value
            $value = $node->getAttribute('value');

            if($this->isCheckable($node)) {

                return $node->getAttribute($this->radioKey);

            } else {

                return $value;

            }

        } elseif($node->tagName === self::NODE_SELECT) {

            # Get select value
            $children = $node->find('[selected]');

            if(!empty($children)) {
                return $children[0]->getAttribute('value');
            };

        } elseif($node->tagName === self::NODE_TEXTAREA) {

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

            if(in_array($node->tagName, [self::NODE_INPUT, self::NODE_BUTTON], true)) {

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

            } elseif($node->tagName === self::NODE_SELECT) {

                if(!$hasValue || $overwrite) {
                    foreach($node->find("option") as $option) {
                        if($value == $option->getAttribute('value')) {
                            $option->setAttribute('selected', 'selected');
                            return true;
                        }
                    };
                }

            } elseif($node->tagName === self::NODE_TEXTAREA) {

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
        } elseif($rowIndex !== null) {
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
                    self::NODE_BUTTON,
                    self::NODE_INPUT,
                    self::NODE_TEXTAREA,
                    self::NODE_SELECT
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

    protected function buildButtonWidget(string $name, string $type, $config): UssElement
    {
        $button = new UssElement(self::NODE_BUTTON);

        if(!in_array($type, [self::TYPE_BUTTON, self::TYPE_SUBMIT])) {
            throw new \Exception(
                sprintf(
                    "%s::%s can only have type '%s' or '%s'",
                    __CLASS__,
                    'NODE_BUTTON',
                    self::TYPE_BUTTON,
                    self::TYPE_SUBMIT
                )
            );
        }

        $button->setAttribute('type', $type);
        $button->setAttribute('class', $config['class'] ?? 'btn btn-primary');

        if(array_key_exists('value', $config)) {
            $this->setValue($button, $config['value']);
        }

        if($button->hasAttribute('value') || !empty($config['use_name'])) {
            $button->setAttribute('name', $name);
        };

        $button->setContent($config['content'] ?? "Submit");

        return $button;
    }

    protected function buildInputWidget(string $name, string $type, array $config): UssElement
    {
        $input = new UssElement(self::NODE_INPUT);
        $input->setAttribute('name', $name);

        if($this->isCheckable($type)) {

            // For Checkbox or Radio
            if($type === self::TYPE_SWITCH) {
                $type = self::TYPE_CHECKBOX;
                $input->setAttribute('role', self::TYPE_SWITCH);
            };

            $input->setAttribute('type', $type);

            if(array_key_exists('checked', $config)) {
                $this->setValue($input, $config['checked']);
            }

            $input->setAttribute('class', 'form-check-input ' . ($config['class'] ?? ''));

            if(!empty($config['value'])) {
                $input->setAttribute('value', $config['value']);
            }

        } else {

            $input->setAttribute('type', $type);
            $input->setAttribute('class', 'form-control ' . ($config['class'] ?? ''));

            if(array_key_exists('value', $config)) {
                $this->setValue($input, $config['value']);
            }

        };

        return $input;
    }

    protected function buildSelectWidget(string $name, array $options, array $config): UssElement
    {
        $select = new UssElement(self::NODE_SELECT);
        $select->setAttribute('name', $name);
        $select->setAttribute('class', 'form-select ' . ($config['class'] ?? ''));
        foreach($options as $value => $display) {
            $option = new UssElement(self::NODE_OPTION);
            $option->setAttribute('value', $value);
            $option->setContent($display);
            $select->appendChild($option);
        };
        if(array_key_exists('value', $config)) {
            $this->setValue($select, $config['value']);
        }
        return $select;
    }

    protected function buildTextareaWidget(string $name, array $config): UssElement
    {
        $textarea = new UssElement(self::NODE_TEXTAREA);
        $textarea->setAttribute('name', $name);
        $textarea->setAttribute('class', 'form-control ' . ($config['class'] ?? ''));
        if(array_key_exists('value', $config)) {
            $this->setValue($textarea, $config['value']);
        }
        return $textarea;
    }

    protected function buildField(string $name, UssElementInterface $widget, array $config): UssElement
    {
        $label = $config['label'] ?? call_user_func(function () use ($name) {
            preg_match_all("/[a-z0-9_\-]+/i", $name, $matches);
            $label = array_map(function ($value) {
                $value = str_replace("_", " ", $value);
                return ucwords(trim($value));
            }, ($matches[0] ?? []));
            return implode(" ", $label);
        });

        $field = [
            'widget' => $widget,
            'column' => (new UssElement(self::NODE_DIV))->setAttribute('class', $config['column'] ?? 'col-md-12 mb-3'),
        ];

        if($this->isCheckable($widget)) {
            $field = $this->constructCheckableField($field, $name, $label, $config);
        } else {
            $field = $this->constructRegularField($field, $name, $label, $config);
        };

        return $this->concludeField($field, $name, $config);
    }

    /**
     * @method constructRegularField
     */
    protected function &constructRegularField(array &$field, string $name, string $label, array $config): array
    {
        $field['group'] = (new UssElement(self::NODE_DIV))->setAttribute('class', 'input-single');
        $field['label'] = (new UssElement(self::NODE_LABEL))
            ->setAttribute('class', $config['label_class'] ?? 'form-label')
            ->setContent($label);
        $field['report'] = (new UssElement(self::NODE_DIV))->setAttribute('class', 'form-text form-report');

        $this->updateReport($field['report'], $config['report'] ?? null);

        $field['column']->appendChild($field['label']);
        $field['column']->appendChild($field['group']);
        $field['column']->appendChild($field['report']);
        $field['group']->appendChild($field['widget']);

        if(isset($config['group'])) {
            $this->buildFieldGroup($field['group'], $config['group']);
        };

        return $field;
    }

    /**
     * @method constructCheckableField
     */
    protected function &constructCheckableField(array &$field, string $name, string $label, array $config): array
    {
        $groupclass = 'form-check';

        if($field['widget']->getAttribute('role') === self::TYPE_SWITCH) {
            $groupclass .= ' form-switch';
        };

        $field['group'] = (new UssElement(self::NODE_DIV))->setAttribute('class', $groupclass);
        $field['label'] = (new UssElement(self::NODE_LABEL))
            ->setAttribute('class', $config['label_class'] ?? 'form-check-label')
            ->setContent($label);
        $field['column']->appendChild($field['group']);
        $field['group']->appendChild($field['widget']);
        $field['group']->appendChild($field['label']);

        return $field;
    }

    /**
     * @method buildButtonField
     */
    protected function buildButtonField(string $name, UssElement $widget, array $config): UssElement
    {
        $field = [
            'column' => (new UssElement(self::NODE_DIV))->setAttribute('class', $config['column'] ?? 'col-md-12 mb-3'),
            'widget' => $widget
        ];

        $field['column']->appendChild($field['widget']);

        return $this->concludeField($field, $name, $config);
    }

    /**
     * @method buildHiddenField
     */
    protected function buildHiddenField(string $name, UssElement $widget, array $config): UssElement
    {
        $field = ['widget' => $widget];
        return $this->concludeField($field, $name, $config, 'widget');
    }

    /**
     * @method makeIdentity
     */
    protected function makeIdentity(string $name, ?string $part): string
    {

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
     * @method buildFieldElements
     */
    private function buildFieldElements(string $name, string $nodeName, array|string|null $context, array $config): UssElement
    {
        switch($nodeName) {
            case self::NODE_TEXTAREA:
                $widget = $this->buildTextareaWidget($name, $config);
                break;
            case self::NODE_SELECT:
                $widget = $this->buildSelectWidget($name, $context ?? [], $config);
                break;
            case self::NODE_BUTTON:
                $widget = $this->buildButtonWidget($name, $context ?? self::TYPE_BUTTON, $config);
                break;
            default:
                $widget = $this->buildInputWidget($name, $context ?? self::TYPE_TEXT, $config);
        }

        $widget = $this->configureWidget($widget, $config);

        if($widget->tagName === self::NODE_BUTTON) {
            $fieldSet = $this->buildButtonField($name, $widget, $config);
        } elseif(
            $widget->hasAttributeValue('type', self::TYPE_HIDDEN) && 
            $widget->tagName === self::NODE_INPUT
        ) {
            $fieldSet = $this->buildHiddenField($name, $widget, $config);
        } else {
            $fieldSet = $this->buildField($name, $widget, $config);
        }

        return $fieldSet;
    }

    /**
     * @method setElementId
     */
    private function setElementId(string $name, ?string $part, UssElement $node): void
    {
        if($node->hasAttribute('id')) {
            return;
        };

        $identity = $this->makeIdentity($name, $part);

        $node->setAttribute("id", $identity);

    }

    /**
     * @method buildFieldGroup
     */
    private function buildFieldGroup(UssElement $node, $groupConfig): void
    {
        if(!is_array($groupConfig)) {
            $groupConfig = ['append' => $groupConfig];
        };

        $default = ['prepend', 'append'];

        foreach($default as $index) {
            $value = $groupConfig[$index] ?? null;
            if(!is_null($value) && (is_scalar($value) || ($value instanceof UssElement))) {
                if(is_scalar($value)) {
                    $element = (new UssElement(self::NODE_DIV))->setAttribute('class', 'input-group-text');
                    $element->setContent($value);
                } else {
                    $element = $value;
                    $autoFillButton = [
                        'type' => self::TYPE_BUTTON,
                        'class' => 'btn btn-outline-secondary'
                    ];
                    foreach($autoFillButton as $key => $attrValue) {
                        if($element->tagName === self::NODE_BUTTON && !$element->hasAttribute($key)) {
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

    /**
     * @method updateReport
     */
    private function updateReport(UssElement $node, $config): void
    {
        if(!is_null($config) && (is_array($config) || is_scalar($config))) {
            if(is_scalar($config)) {
                $config = ['message' => $config];
            };
            if(is_scalar($config['message']) && !is_bool($config['message'])) {
                $node->setContent($config['message']);
                if(!empty($config['class']) && is_scalar($config['class'])) {
                    $node->addAttributeValue('class', $config['class']);
                };
            };
        };
    }

    /**
     * @method configureWidget
     */
    private function configureWidget(UssElement $widget, array $config): UssElement
    {
        if(!empty($config['id'])) {
            if(preg_match("/\w(?:[a-z0-9_\-]+)?/", $config['id'])) {
                $widget->setAttribute('id', $config['id']);
            };
        };

        if(!empty($config['required'])) {
            $widget->setAttribute('required', 'required');
        }

        if(!empty($config['attr']) && is_array($config['attr'])) {
            foreach($config['attr'] as $key => $value) {
                if(in_array($key, ['type'])) {
                    continue;
                }
                if(is_numeric($key)) {
                    $key = $value;
                }
                $widget->setAttribute($key, $value);
            };
        }

        if(!empty($config['ignore'])) {
            $widget->removeAttribute('name');
        };

        return $widget;
    }

    /**
     * @method isCheckable
     */
    private function isCheckable(string|UssElement $entity): bool
    {
        if(!is_string($entity)) {
            if($entity->tagName !== self::NODE_INPUT) {
                return false;
            }
            $value = $entity->getAttribute('type');
        } else {
            $value = $entity;
        }
        return in_array($value, [self::TYPE_CHECKBOX, self::TYPE_SWITCH, self::TYPE_RADIO]);
    }

    /**
     * @method concludeField
     */
    private function &concludeField(array &$field, string &$name, array &$config, string $parentKey = 'column'): UssElement
    {
        $elements = ['column', 'widget', 'report'];

        foreach($elements as $key) {
            $element = $field[$key] ?? null;
            if(!empty($element)) {
                $this->setElementId($name, $key, $element);
                if($key === 'widget' && $this->isCheckable($element)) {
                    if(!empty($field['label'])) {
                        $field['label']->setAttribute('for', $element->getAttribute('id'));
                    }
                }
            };
        };

        if(is_callable($config['fields'] ?? null)) {
            call_user_func($config['fields'], $field);
        };

        return $field[$parentKey];
    }

    /**
     * Recursively flattens a multi-dimensional array and constructs keys in the specified format.
     *
     * @param array  $array   The multi-dimensional array to flatten.
     * @param string $prefix  (Optional) The prefix to prepend to keys.
     *
     * @return array The flattened array with keys in the specified format.
     */
    private function flattenArray($value, ?string $key = null)
    {
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
