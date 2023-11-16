<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

abstract class AbstractUssFormField implements UssFormFieldInterface
{
    private static int $count = 0;

    public readonly string $widgetId;

    protected array $validNodeNames = [
        UssElement::NODE_INPUT,
        UssElement::NODE_SELECT,
        UssElement::NODE_TEXTAREA,
        UssElement::NODE_BUTTON
    ];

    /**
     * Containers: No values but has attributes and holds elements
     */
    protected array $row = [
        'element' => null,
    ];

    protected array $container = [
        'element' => null,
    ];

    protected array $widgetContainer = [
        'element' => null,
    ];

    protected array $info = [
        'element' => null,
        'value' => null,
        'hidden' => 'false'
    ];

    protected array $label = [
        'element' => null,
        'value' => null,
        'hidden' => false
    ];

    protected array $validation = [
        'element' => null,
        'value' => null,
        'type' => self::VALIDATION_ERROR,
        'icon' => null,
        'hidden' => false
    ];

    protected array $widget = [
        'element' => null,
        'value' => null,
        'appendant' => null,
        'prependant' => null,
        'options' => [
            'values' => [],
            'elements' => [],
        ]
    ];

    /**
     * @method __constuct
     */
    public function __construct(
        public readonly string $nodeName = UssElement::NODE_INPUT,
        protected ?string $nodeType = UssForm::TYPE_TEXT
    ) {
        $this->widgetId = $this->generateId();
        $this->generateElements();
    }

    /**
     * @method __debugInfo
     */
    public function __debugInfo()
    {
        $debugger = [];
        $skip = ['validNodeNames'];

        foreach((new \ReflectionClass($this))->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            if(!in_array($name, $skip)) {
                $value = $property->getValue($this);
                if($value instanceof UssElement) {
                    $value = 'object(' . $value::class . ')';
                } elseif($name === 'widgetOptions') {
                    $value = $value['values'];
                }
                $debugger[$name] = $value;
            }
        }

        return $debugger;
    }

    /**
     * Protected Methods
     */
    protected function generateElements(): void
    {
        $elements = [
            'row' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'col-12',
                ],
            ],
            'container' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'field-container',
                ],
            ],
            'widgetContainer' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'widget-container my-1 ' . call_user_func(function () {
                        $class = '';
                        if($this->isCheckable()) {
                            $class = 'form-check ';
                            if($this->nodeType === UssForm::TYPE_SWITCH) {
                                $class .= 'form-switch';
                            }
                        }
                        return $class;
                    })
                ],
            ],
            'info' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-info small text-secondary'
                ],
            ],
            'label' => [
                UssElement::NODE_LABEL,
                'attributes' => [
                    'class' => $this->isCheckable() ? 'form-check-label' : 'form-label',
                    'for' => $this->widgetId
                ],
            ],
            'validation' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-validity small d-flex'
                ],
            ],
        ];

        foreach($elements as $name => $prop) {
            $this->{$name}['element'] = new UssElement($prop[0]);
            foreach($prop['attributes'] as $key => $value) {
                $this->{$name}['element']->setAttribute($key, $value);
            }
        }

        $this->buildWidgetElement();

        if($this->isHiddenWidget()) {
            $this->setLabelHidden(true);
            $this->setInfoHidden(true);
            $this->setValidationHidden(true);
        }
    }

    /**
     * @method buildWidgetElement
     */
    protected function buildWidgetElement(): void
    {
        if(!in_array($this->nodeName, $this->validNodeNames)) {
            throw new \Exception(
                sprintf(
                    "Invalid nodename %s in __construct() argument #2; valid nodenames are %s",
                    $this->nodeName,
                    implode(", ", $this->validNodeNames)
                )
            );
        }

        $this->widget['element'] = new UssElement($this->nodeName);
        $this->widget['element']->setAttribute('id', $this->widgetId);
        $this->widget['element']->setAttribute('required', 'required');

        switch($this->nodeName) {

            case UssElement::NODE_SELECT:
                $this->widget['element']->setAttribute('class', 'form-select');
                break;

            case UssElement::NODE_BUTTON:
                $this->widget['element']->setAttribute('class', 'btn btn-primary');
                $nodeType = ($this->nodeType != UssForm::TYPE_SUBMIT) ? UssForm::TYPE_BUTTON : $this->nodeType;
                $this->widget['element']->setAttribute('type', $nodeType);
                $this->widget['element']->setContent('Submit');
                break;

            case UssElement::NODE_TEXTAREA:
                $this->widget['element']->setAttribute('class', 'form-control');
                break;

            default:

                switch($this->nodeType) {

                    case UssForm::TYPE_CHECKBOX:
                    case UssForm::TYPE_RADIO:
                    case UssForm::TYPE_SWITCH:
                        $this->widget['element']->setAttribute('class', 'form-check-input');
                        break;

                    case UssForm::TYPE_BUTTON:
                    case UssForm::TYPE_SUBMIT:
                        $this->widget['element']->setAttribute('class', 'btn btn-primary');
                        break;

                    default:
                        $this->widget['element']->setAttribute('class', 'form-control');
                }

                $nodeType = $this->nodeType;

                if($nodeType === UssForm::TYPE_SWITCH) {
                    $nodeType = UssForm::TYPE_CHECKBOX;
                };

                $this->widget['element']->setAttribute('type', $nodeType);
        }
    }

    /**
     * @method isCheckable
     */
    public function isCheckable(): bool
    {
        return $this->nodeName === UssElement::NODE_INPUT &&
        in_array($this->nodeType, [
            UssForm::TYPE_CHECKBOX,
            UssForm::TYPE_RADIO,
            UssForm::TYPE_SWITCH
        ]);
    }

    /**
     * @method isButton
     */
    public function isButton(): bool
    {
        if($this->nodeName === UssElement::NODE_BUTTON) {
            return true;
        } elseif($this->nodeName === UssElement::NODE_INPUT) {
            return in_array(
                $this->nodeType,
                [
                    UssForm::TYPE_SUBMIT,
                    UssForm::TYPE_BUTTON
                ]
            );
        }
        return false;
    }

    /**
     * @method isHiddenWidget
     */
    public function isHiddenWidget(): bool
    {
        return
            $this->nodeName === UssElement::NODE_INPUT &&
            $this->nodeType === UssForm::TYPE_HIDDEN;
    }

    /**
     * @method insertElementValue
     */
    protected function insertElementValue(UssElement $element, UssElement|string|null $value, ?string $icon = null): void
    {
        $element->freeElement();

        if(!is_null($value)) {

            $spanNode = new UssElement(UssElement::NODE_SPAN);

            if($icon) {
                $iconNode = new UssElement(UssElement::NODE_I);
                $iconNode->setAttribute('class', $icon . ' me-1');
            } else {
                $iconNode = null;
            }

            if($value instanceof UssElement) {

                if($iconNode) {
                    $spanNode->appendChild($value);
                    $element->appendChild($iconNode);
                    $element->appendChild($spanNode);
                } else {
                    $element->appendChild($value);
                }

            } else {

                if($iconNode) {
                    $spanNode->setContent($value);
                    $content = ($icon ? $iconNode->getHTML() : '') . $spanNode->getHTML();
                    $element->setContent($content);
                } else {
                    $element->setContent($value);
                }

            }
        };
    }

    /**
     * @method insertWidgetValue
     */
    protected function insertWidgetValue(): void
    {
        switch($this->widget['element']->nodeName) {
            case UssElement::NODE_TEXTAREA:
                $this->widget['element']->setContent($this->widget['value']);
                break;
            case UssElement::NODE_SELECT:
                $key = array_search($this->widget['value'], $this->widget['options']['values']);
                if($key !== false) {
                    $optionElement = $this->widget['options']['elements'][$key] ?? null;
                    if($optionElement) {
                        $optionElement->setAttribute('selected', 'selected');
                    }
                }
                break;
            default:
                $this->widget['element']->setAttribute('value', $this->widget['value']);
        }
    }

    /**
     * @method refactorExpendant
     */
    protected function refactorInputGroupContent(null|string|UssElement $inputGroupContent): UssElement
    {
        if(!is_null($inputGroupContent)) {

            $spanElement = new UssElement(UssElement::NODE_SPAN);
            $spanElement->setAttribute('class', 'input-group-text');

            if($inputGroupContent instanceof UssElement) {
                if($inputGroupContent->tagName !== UssElement::NODE_BUTTON) {
                    $element = $spanElement;
                    $element->appendChild($inputGroupContent);
                } else {
                    $element = $inputGroupContent;
                    if(!$element->hasAttribute('type')) {
                        $element->setAttribute('type', 'button');
                    }
                }
            } else {
                $element = $spanElement;
                $element->setContent($inputGroupContent);
            }

        } else {

            $element = null;

        }

        return $element;
    }

    /**
     * @method generateId
     */
    public function generateId(): string
    {
        self::$count++;
        $id = strtolower('uff-' . $this->nodeName . '-0' . self::$count);
        return $id;
    }

    /**
     * @method attributeSetter
     */
    protected function attributeSetter(UssElement $element, string $name, ?string $value, bool $append): self
    {
        if($append) {
            $element->addAttributeValue($name, $value);
        } else {
            $element->setAttribute($name, $value);
        }
        return $this;
    }

    /**
     * @method attributeRemover
     */
    protected function attributeRemover(UssElement $element, string $name, ?string $detach): self
    {
        if(is_null($detach)) {
            $element->removeAttribute($name);
        } else {
            $element->removeAttributeValue($name, $detach);
        }
        return $this;
    }

    /**
     * @method updateWidgetElementOptions
     */
    protected function rebuildWidgetOptionsElements(array $options): void
    {
        $this->widget['element']->freeElement();
        foreach($options as $key => $output) {
            $option = $this->createOptionElement($key, $output);
            $this->widget['options']['elements'][$key] = $option;
            $this->widget['element']->appendChild($option);
        }
    }

    /**
     * @method createWidgetOption
     */
    protected function createOptionElement(string $value, string $output): UssElement
    {
        $option = new UssElement(UssElement::NODE_OPTION);
        $option->setAttribute('value', $value);
        $option->setContent($output);
        return $option;
    }

    /**
     * @method validationHue
     */
    protected function validationExec(): void
    {
        $validationView = [
            self::VALIDATION_SUCCESS => [
                'class' => 'text-success is-valid',
                'icon' => 'bi bi-check-circle'
            ],
            self::VALIDATION_ERROR => [
                'class' => 'text-danger is-invalid',
                'icon' => 'bi bi-exclamation-triangle'
            ]
        ];

        foreach($validationView as $validationType => $attr) {
            if($this->validation['type'] === $validationType) {
                $this->validation['element']->addAttributeValue('class', $attr['class']);
                $this->validation['icon'] = $attr['icon'];
            } else {
                $this->validation['element']->removeAttributeValue('class', $attr['class']);
            }
        }

        $this->insertElementValue(
            $this->validation['element'],
            $this->validation['value'],
            $this->validation['icon']
        );
    }
}
