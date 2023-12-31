<?php

namespace Ucscode\UssForm\Resource\Service;

use Ucscode\UssForm\Gadget\Gadget;

class FieldUtils extends AbstractFieldUtils
{
    protected static int $fieldIndex = 1;

    public function welcomeGadget(string $name, Gadget $gadget): void
    {
        if(!$gadget->isFixed()) {

            // Update Widget Context
            if(!$gadget->widget->isFixed()) {

                !$gadget->widget->hasAttribute('name') ?
                    $gadget->widget->setAttribute('name', $name) : null;

                !$gadget->widget->hasAttribute('id') ?
                    $gadget->widget->setAttribute('id', 'field-widget-' . self::$fieldIndex++) : null;
            }

            // Update Label gadget
            if(!$gadget->label->isFixed()) {

                !$gadget->label->hasValue() ?
                    $gadget->label->setValue(ucwords($this->simplifyContent($name))) : null;

                !$gadget->label->hasAttribute('for') && $gadget->widget->hasAttribute('id') ?
                    $gadget->label->setAttribute('for', $gadget->widget->getAttribute('id')) : null;
            }
        }
    }
}