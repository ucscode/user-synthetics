<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Abstraction\AbstractUssFormField;

class UssFormField extends AbstractUssFormField
{
    protected string $prefix = 'primary-field';
    protected ?UssElement $lineBreak = null;

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
