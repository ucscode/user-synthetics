<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Abstraction\AbstractUssFormField;
use Ucscode\UssForm\Trait\FieldContainerTrait;
use Ucscode\UssForm\Trait\FieldInfoTrait;
use Ucscode\UssForm\Trait\FieldLabelTrait;
use Ucscode\UssForm\Trait\FieldRowTrait;
use Ucscode\UssForm\Trait\FieldValidationTrait;
use Ucscode\UssForm\Trait\FieldWidgetContainerTrait;
use Ucscode\UssForm\Trait\FieldWidgetTrait;

class UssFormField extends AbstractUssFormField
{
    /**
     * @method getFieldAsElement
     */
    public function getFieldAsElement(): UssElement
    {
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
