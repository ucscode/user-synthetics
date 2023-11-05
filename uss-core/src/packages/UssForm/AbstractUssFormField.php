<?php

namespace Ucscode\UssForm;

use ReflectionProperty;
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
    protected UssElement $rowElement;
    protected UssElement $containerElement;
    protected UssElement $widgetContainerElement;

    /**
     * Elements: Has attributes and contain values
     */
    protected UssElement $infoElement;
    protected UssElement $labelElement;
    protected UssElement $validationElement;
    protected UssElement $widgetElement;

    /**
     * $values: The values for each elements
     */
    protected null|string|UssElement $labelValue = null;
    protected null|string|UssElement $infoValue = null;
    protected ?string $infoIcon = null;
    protected ?string $validationValue = null;
    protected ?string $validationType = self::VALIDATION_ERROR;
    protected ?string $validationIcon = null;
    protected ?string $widgetValue = null;

    /**
     * Widget Group: append or prepend gadget (icon, button etc) to widgets
     */
    protected ?UssElement $widgetAppendant = null;
    protected ?UssElement $widgetPrependant = null;

    /**
     * Other Widget Resource
     */
    protected array $widgetOptions = [
        'values' => [],
        'elements' => []
    ];

    /**
     * @method __constuct
     */
    public function __construct(
        public readonly string $fieldName,
        public readonly string $nodeName = UssForm::NODE_INPUT,
        protected ?string $nodeType = UssForm::TYPE_TEXT
    ) {
        $this->labelValue = $this->labelize($this->fieldName);
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
                } elseif( $name === 'widgetOptions' ) {
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
            'rowElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'col-12',
                    'data-field' => $this->fieldName
                ],
            ],
            'containerElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'field-container',
                ],
            ],
            'widgetContainerElement' => [
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
            'infoElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-info small text-secondary'
                ],
            ],
            'labelElement' => [
                UssElement::NODE_LABEL,
                'attributes' => [
                    'class' => $this->isCheckable() ? 'form-check-label' : 'form-label',
                    'for' => $this->widgetId
                ],
            ],
            'validationElement' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-validity small '
                ],
            ],
        ];

        foreach($elements as $name => $prop) {
            $this->{$name} = new UssElement($prop[0]);
            foreach($prop['attributes'] as $key => $value) {
                $this->{$name}->setAttribute($key, $value);
            }
        }

        $this->buildWidgetElement();
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

        $this->widgetElement = new UssElement($this->nodeName);
        $this->widgetElement->setAttribute('name', $this->fieldName);
        $this->widgetElement->setAttribute('id', $this->widgetId);

        switch($this->nodeName) {

            case UssElement::NODE_SELECT:
                $this->widgetElement->setAttribute('class', 'form-select');
                break;

            case UssElement::NODE_BUTTON:
                $this->widgetElement->setAttribute('class', 'btn btn-primary');
                $this->widgetElement->setAttribute('type', 'button');
                $this->widgetElement->setContent($this->fieldName);
                break;

            case UssElement::NODE_TEXTAREA:
                $this->widgetElement->setAttribute('class', 'form-control');
                break;

            default:

                switch($this->nodeType) {

                    case UssForm::TYPE_CHECKBOX:
                    case UssForm::TYPE_RADIO:
                    case UssForm::TYPE_SWITCH:
                        $this->widgetElement->setAttribute('class', 'form-check-input');
                        break;

                    case UssForm::TYPE_BUTTON:
                    case UssForm::TYPE_SUBMIT:
                        $this->widgetElement->setAttribute('class', 'btn btn-primary');
                        if($this->nodeType === UssForm::TYPE_BUTTON) {
                            $this->widgetElement->setAttribute('value', $this->fieldName);
                        }
                        break;

                    default:
                        $this->widgetElement->setAttribute('class', 'form-control');
                }

                $nodeType = $this->nodeType;

                if($nodeType === UssForm::TYPE_SWITCH) {
                    $nodeType = UssForm::TYPE_CHECKBOX;
                };

                $this->widgetElement->setAttribute('type', $nodeType);
        }
    }

    /**
     * @method isCheckable
     */
    protected function isCheckable(): bool
    {
        return $this->nodeName === UssForm::NODE_INPUT &&
        in_array($this->nodeType, [
            UssForm::TYPE_CHECKBOX,
            UssForm::TYPE_RADIO,
            UssForm::TYPE_SWITCH
        ]);
    }

    /**
     * @method isButton
     */
    protected function isButton(): bool
    {
        if($this->nodeName === UssForm::NODE_BUTTON) {
            return true;
        } else if($this->nodeName === UssForm::NODE_INPUT) {
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
     * @method insertElementValue
     */
    protected function insertElementValue(string $name, ?string $icon = null): void
    {
        $element = $this->{$name . 'Element'};
        $value = $this->{$name . 'Value'};
        $span = new UssElement(UssElement::NODE_SPAN);

        if($icon) {
            $icon = (new UssElement(UssElement::NODE_I))->setAttribute('class', $icon . ' me-1');
        }

        if(!is_null($value)) {
            if($value instanceof UssElement) {
                if($icon) {
                    $element->appendChild($icon);
                    $element->appendChild($span);
                    $span->appendChild($value);
                } else {
                    $element->appendChild($value);
                }
            } else {
                if($icon) {
                    $span->setContent($value);
                    $element->setContent(
                        ($icon ? $icon->getHTML() : '') .
                        $span->getHTML()
                    );
                } else {
                    $element->setContent($value);
                }
            }
        };
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
     * @method labelize
     */
    protected function labelize(string $label): string
    {
        $entity = ['[', ']', '_'];
        $with = ['', '', ' '];
        return ucfirst(str_replace($entity, $with, $label));
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
    protected function attributeSetter(UssElement $element, string $name, string $value, bool $append): self
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
        $this->widgetElement->freeElement();
        foreach($options as $key => $output) {
            $option = $this->createOptionElement($key, $output);
            $this->widgetOptions['elements'][$key] = $option;
            $this->widgetElement->appendChild($option);
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
}
