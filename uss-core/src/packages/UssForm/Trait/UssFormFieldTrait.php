<?php

namespace Ucscode\UssForm\Trait;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\Field\ValidationInterface;
use Ucscode\UssForm\UssForm;

trait UssFormFieldTrait
{
    protected const PRIMARY_NODES = [
        UssElement::NODE_INPUT,
        UssElement::NODE_SELECT,
        UssElement::NODE_TEXTAREA,
        UssElement::NODE_BUTTON
    ];

    public readonly string $widgetId;
    private static int $count = 0;

    /**
     * @method generateId
     */
    public function generateId(): string
    {
        self::$count++;
        $id = $this->prefix . '-' . $this->nodeName . '-' . self::$count;
        return strtolower($id);
    }

    protected function getElementStructure(): array
    {
        return [
            'row' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'col-12 mt-1 mb-2',
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
                    'class' => $this->widgetContainerClass()
                ],
            ],
            'info' => [
                UssElement::NODE_DIV,
                'attributes' => [
                    'class' => 'form-info small text-secondary d-flex align-items-baseline'
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
                    'class' => 'form-validity small d-flex align-items-baseline'
                ],
            ],
        ];
    }

    protected function generateElements(): void
    {
        $this->widgetId = $this->generateId();

        foreach($this->getElementStructure() as $name => $prop) {
            # $this->{name} === $this->label | $this->widget | ...
            $this->{$name}['element'] = new UssElement($prop[0]);
            foreach($prop['attributes'] as $key => $value) {
                $this->{$name}['element']->setAttribute($key, $value);
            }
        }

        $this->buildWidgetElement();
    }

    /**
     * @method buildWidgetElement
     */
    protected function buildWidgetElement(): void
    {
        if(!in_array($this->nodeName, self::PRIMARY_NODES)) {
            throw new \Exception(
                sprintf(
                    "Invalid nodename %s in __construct() argument #2; valid nodenames are %s",
                    $this->nodeName,
                    implode(", ", self::PRIMARY_NODES)
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

    protected function buildFieldStructure(): void
    {
        $this->row['element']->appendChild($this->container['element']);

        /**
         * - Buttons cannot have label element
         * - Checkables can only have labels after the widget element
         */
        if(!$this->isButton() && !$this->isCheckable()) {
            $this->container['element']->appendChild($this->label['element']);
        }

        $this->container['element']->appendChild($this->info['element']);
        $this->container['element']->appendChild($this->widgetContainer['element']);
        $this->widgetContainer['element']->appendChild($this->widget['element']);

        if(!$this->isButton()) {
            if($this->isCheckable()) {
                $this->widgetContainer['element']->appendChild($this->label['element']);
            }
            $this->container['element']->appendChild($this->validation['element']);
        }
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
                $key = $this->widget['value'];
                $exists = array_key_exists($key, $this->getWidgetOptions());
                if($exists) {
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
            ValidationInterface::VALIDATION_SUCCESS => [
                'class' => 'text-success is-valid',
                'icon' => 'bi bi-check-circle'
            ],
            ValidationInterface::VALIDATION_ERROR => [
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

    /**
     * @method extendWidgetAside
     */
    protected function extendWidgetAside(callable $func): void
    {
        if(!$this->isCheckable() && !$this->isButton()) {
            $this->widgetContainer['element']
                ->addAttributeValue('class', 'input-group', true)
                ->removeAttributeValue('class', 'input-single', true);
            $func();
        }
    }

    /**
     * @method
     */
    protected function widgetContainerClass(): string
    {
        $class = 'widget-container ';
        if($this->isCheckable()) {
            $class .= 'form-check ';
            if($this->nodeType === UssForm::TYPE_SWITCH) {
                $class .= 'form-switch';
            }
        }
        return $class;
    }
}
