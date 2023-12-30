<?php

namespace Ucscode\UssForm\Resource;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;

class FormUtils
{
    public function isCheckable($element): bool
    {
        return $element instanceof UssElement &&
            $element->nodeName === Field::NODE_INPUT &&
            in_array(
                $element->getAttribute('type'),
                [
                    Field::TYPE_CHECKBOX,
                    Field::TYPE_RADIO
                ]
            );
    }

    public function isButton($element): bool
    {
        return $element instanceof UssElement && (
            $element->nodeName === Field::NODE_BUTTON ||
            (
                $element->nodeName === Field::NODE_INPUT &&
                in_array(
                    $element->getAttribute('type'),
                    [
                        Field::TYPE_BUTTON,
                        Field::TYPE_SUBMIT
                    ]
                )
            )
        );
    }
}