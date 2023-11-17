<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Abstraction\AbstractUssFormField;

class UssFormField extends AbstractUssFormField
{
    protected string $prefix = 'primary-field';
    
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
