<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\UssFormFieldInterface;
use Ucscode\UssForm\Trait\UssFormFieldTrait;
use Ucscode\UssForm\Trait\Field\FieldContainerTrait;
use Ucscode\UssForm\Trait\Field\FieldInfoTrait;
use Ucscode\UssForm\Trait\Field\FieldLabelTrait;
use Ucscode\UssForm\Trait\Field\FieldRowTrait;
use Ucscode\UssForm\Trait\Field\FieldValidationTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetContainerTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetMinorTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetTrait;

class UssFormField implements UssFormFieldInterface
{
    /** Field Traits that implements the method of UssFormFieldInterface */
    use FieldRowTrait;
    use FieldContainerTrait;
    use FieldInfoTrait;
    use FieldLabelTrait;
    use FieldWidgetContainerTrait;
    use FieldWidgetTrait;
    use FieldWidgetMinorTrait;
    use FieldValidationTrait;

    /** UssFormField Method */
    use UssFormFieldTrait;

    protected string $prefix = 'primary-field';
    protected ?UssElement $lineBreak = null;

    /**
     * @method __constuct
     */
    public function __construct(
        public readonly string $nodeName = UssForm::NODE_INPUT,
        protected ?string $nodeType = UssForm::TYPE_TEXT
    ) {
        $this->generateElements();
        $this->buildFieldStructure();
    }

    /**
     * @method __debugInfo
     */
    public function __debugInfo()
    {
        $debugger = [];
        $skip = [];

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
     * @method addLineBreak
     */
    public function addLineBreak(): self
    {
        if(!$this->hasLineBreak()) {
            $this->lineBreak = new UssElement(UssElement::NODE_DIV);
            $this->lineBreak->setAttribute('class', 'line-break');
        }
        return $this;
    }

    /**
     * @method getLineBreak
     */
    public function getLineBreak(): ?UssElement
    {
        return $this->lineBreak;
    }

    /**
     * @method hasLineBreak
     */
    public function hasLineBreak(): bool
    {
        return !empty($this->lineBreak);
    }

    /**
     * @method removeLineBreak
     */
    public function removeLineBreak(): self
    {
        if($this->hasLineBreak()) {
            $parentElement = $this->lineBreak->getParentElement();
            if($parentElement) {
                $parentElement->removeChild($this->lineBreak);
            };
            $this->lineBreak = null;
        }
        return $this;
    }

    /**
     * @method getFieldAsElement
     */
    public function getFieldAsElement(): UssElement
    {
        if($this->isWidgetHidden()) {
            $this->setLabelHidden(true);
            $this->setInfoHidden(true);
            $this->setValidationHidden(true);
        }
        return $this->row['element'];
    }

    /**
     * @method getFieldAsHTML
     */
    public function getFieldAsHTML(): string
    {
        return $this->getFieldAsElement()->getHTML(true);
    }
}
