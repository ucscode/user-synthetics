<?php

namespace Ucscode\UssForm\Field;

use Ucscode\UssForm\Field\Manifest\AbstractField;
use Ucscode\UssForm\Field\Manifest\ElementContext;

class Field extends AbstractField
{
    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }

    // Secondary Field
    public function addPartner()
    {

    }

    public function getPartner()
    {

    }

    public function removePartner()
    {

    }

    public function getPartners()
    {
        
    }
}
