<?php

class UssForm extends UssElementBuilder implements UssFormInterface
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
    private string $radioKey = 'data-checked';

    public function __construct(string $name, ?string $route = null, string $method = 'GET', string $enctype = null)
    {
        parent::__construct(self::NODE_FORM);
        $this->setAttribute('name', $name);
        $this->setAttribute('action', $route);
        $this->setAttribute('method', $method);
        if(!empty($enctype)) {
            $this->setAttribute('enctype', $enctype);
        };
        $this->setElementId("_ussf_" . $name, null, $this);
    }

    public function isVoid(bool $void): self
    {
        $this->void = false;
        return $this;
    }

    /**
     * Add Input Field
     *
     * @param string $name The name of the field
     *
     * @param string $fieldType The type of file; Possible values include
     * UssForm::INPUT, UssForm::SELECT, UssForm::TEXTAREA, UssForm::BUTTON"
     *
     * @param string|array|null $context
     * - UssForm::INPUT - Context is a string that defines the field type e.g UssForm::TYPE_TEXT, UssForm::TYPE_NUMBER...
     * - UssForm::SELECT - Context is an array that defines the field options
     * - UssForm::TEXTAREA - Context is not used
     * - UssForm::BUTTON - Context defines submit button of type UssForm::BUTTON or UssForm::INPUT
     *
     * @param array $data An array of configurations
     */
    public function add(
        string $name,
        string $fieldType,
        array|string|null $context = null,
        array $config = []
    ): UssElementBuilder {

        /**
         * Build Different Widget Base On Provided Field Type
         * Context In Different Area
         *
         * TEXTAREA - Context is Null
         * SELECT - Context is an array with choices
         * BUTTON - Context is either "button" or "input"
         * INPUT - Context is any input type such as "input, number, date, url..."
         */

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

        return $this->appendField($fieldColumn);

    }

    /**
     * Add a new row to the form
     * New fields will be added to the last added row
     */
    public function addRow(string $class = ''): UssElementBuilder
    {
        $row = new UssElementBuilder(self::NODE_DIV);
        $row->setAttribute('class', 'row ' . $class);
        $this->appendChild($row);
        return $row;
    }

    /**
     * Populate the form with data
     *
     * Automatically set the value of fields with populated data
     * Value set directly on the form will override the populated data
     */
    public function populate(array $data)
    {
        $this->populate = $data;
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

    public function getValue(UssElementBuilder $node): ?string
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

    public function setValue(UssElementBuilder $node, $value, bool $overwrite = true): bool
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
     * Append a new field into the current active form row
     */
    public function appendField(UssElementBuilder $column): UssElementBuilder
    {
        if(empty($this->children)) {
            $row = $this->addRow();
        } else {
            $row = $this->lastChild();
            if($row->tagName !== self::NODE_DIV || !$row->hasAttributeValue('class', 'row')) {
                $row = $this->addRow();
            }
        };
        $row->appendChild($column);
        return $column;
    }

    protected function buildButtonWidget(string $name, string $type, $data): UssElementBuilder
    {
        if($type !== self::INPUT) {
            $button = new UssElementBuilder(self::NODE_BUTTON);
        } else {
            $button = new UssElementBuilder(self::NODE_INPUT);
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

    protected function buildInputWidget(string $name, string $type, array $data): UssElementBuilder
    {
        $input = new UssElementBuilder(self::NODE_INPUT);
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

    protected function buildSelectWidget(string $name, array $options, array $data): UssElementBuilder
    {
        $select = new UssElementBuilder(self::NODE_SELECT);
        $select->setAttribute('name', $name);
        $select->setAttribute('class', 'form-select ' . ($data['class'] ?? ''));
        foreach($options as $value => $display) {
            $option = new UssElementBuilder(self::NODE_OPTION);
            $option->setAttribute('value', $value);
            $option->setContent($display);
            $select->appendChild($option);
        };
        if(array_key_exists('value', $data)) {
            $this->setValue($select, $data['value']);
        }
        return $select;
    }

    protected function buildTextareaWidget(string $name, ?string $type, array $data): UssElementBuilder
    {
        $textarea = new UssElementBuilder(self::NODE_TEXTAREA);
        $textarea->setAttribute('name', $name);
        $textarea->setAttribute('class', 'form-control ' . ($data['class'] ?? ''));
        if(array_key_exists('value', $data)) {
            $this->setValue($textarea, $data['value']);
        }
        return $textarea;
    }

    protected function buildField(string $name, UssElementInterface $widget, array $data): UssElementBuilder
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
            'column' => (new UssElementBuilder(self::NODE_DIV))->setAttribute('class', $data['column'] ?? 'col-md-12 mb-3'),
        ];

        if($this->isCheckable($widget)) {
            $field = $this->constructCheckableField($field, $name, $label, $data);
        } else {
            $field = $this->constructRegularField($field, $name, $label, $data);
        };
        
        return $this->concludeField($field, $name, $data);

    }

    protected function &constructRegularField(array &$field, string $name, string $label, array $data): array {

        $field['group'] = (new UssElementBuilder(self::NODE_DIV))->setAttribute('class', 'input-single');

        $field['label'] = (new UssElementBuilder(self::NODE_LABEL))
            ->setAttribute('class', $data['label_class'] ?? 'form-label')
            ->setContent($label);
        
        $field['report'] = (new UssElementBuilder(self::NODE_DIV))->setAttribute('class', 'form-text form-report');

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

        $field['group'] = (new UssElementBuilder(self::NODE_DIV))->setAttribute('class', $groupclass);

        $field['label'] = (new UssElementBuilder(self::NODE_LABEL))
            ->setAttribute('class', 'form-check-label')
            ->setContent($label);

        $field['column']->appendChild($field['group']);
        $field['group']->appendChild($field['widget']);
        $field['group']->appendChild($field['label']);
        
        return $field;
    }

    protected function buildButtonField(string $name, UssElementBuilder $widget, array $data): UssElementBuilder
    {

        $field = [
            'column' => (new UssElementBuilder(self::NODE_DIV))->setAttribute('class', $data['column'] ?? 'col-md-12 mb-3'),
            'widget' => $widget
        ];

        # Create Formation
        $field['column']->appendChild($field['widget']);

        return $this->concludeField($field, $name, $data);
    }

    protected function buildHiddenField(string $name, UssElementBuilder $widget, array $data): UssElementBuilder
    {
        $field = ['widget' => $widget];
        return $this->concludeField($field, $name, $data, 'widget');
    }

    private function setElementId(string $name, ?string $part, UssElementBuilder $node): void
    {
        if($node->hasAttribute('id')) {
            return;
        };

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

        $node->setAttribute("id", $identity);

    }

    private function buildFieldGroup(UssElementBuilder $node, $data): void
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
            } elseif(!is_scalar($value) && !($value instanceof UssElementBuilder)) {
                continue;
            } else {
                if(is_scalar($value)) {
                    $element = (new UssElementBuilder(self::NODE_DIV))->setAttribute('class', 'input-group-text');
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

    private function updateReport(UssElementBuilder $node, $data)
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

    private function configureWidget(UssElementBuilder $widget, array $data): UssElementBuilder
    {
        if(!empty($data['id'])) {
            if(preg_match("/\w(?:[a-z0-9_\-]+)?/", $data['id'])) {
                $widget->setAttribute('id', $data['id']);
            };
        };
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
        return $widget;
    }

    private function isCheckable(string|UssElementBuilder $entity): bool {
        if(!is_string($entity)) {
            if($entity->tagName !== self::INPUT) {
                return false;
            }
            $value = $entity->getAttribute('type');
        } else $value = $entity;
        return in_array($value, [self::TYPE_CHECKBOX, self::TYPE_SWITCH, self::TYPE_RADIO]);
    }

    private function &concludeField(array &$field, string &$name, array &$data, string $__key = 'column'): UssElementBuilder
    {
        # Dedicate Identity
        $selectives = [
            'column',
            'widget',
            'report'
        ];

        foreach($selectives as $part) {
            if(!empty($field[$part])) {
                $this->setElementId($name, $part, $field[$part]);
            };
        };

        if(is_callable($data['fields'] ?? null)) {
            call_user_func($data['fields'], $field);
        };

        return $field[$__key];
    }

}
