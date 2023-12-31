<?php

namespace Ucscode\UssForm\Resource\Service;

use ReflectionClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\FieldTypesInterface;

abstract class AbstractFieldUtils
{
    public function regulateElementPrototype(string $nodeName, ?string $nodeType): array
    {
        $nodeName = in_array(
            $nodeName,
            [
                Field::NODE_SELECT,
                Field::NODE_TEXTAREA,
                Field::NODE_BUTTON
            ]
        ) ? $nodeName : Field::NODE_INPUT;

        $types = (new ReflectionClass(FieldTypesInterface::class))->getConstants();

        if($nodeName === Field::NODE_BUTTON) {
            $nodeType = in_array(
                $nodeType,
                [
                    Field::TYPE_SUBMIT,
                    Field::TYPE_BUTTON
                ]
            ) ? $nodeType : Field::TYPE_BUTTON;
        }

        $nodeType = in_array(
            $nodeName,
            [
                Field::NODE_SELECT,
                Field::NODE_TEXTAREA
            ]
        ) ? null : (in_array($nodeType, $types) ? $nodeType : Field::TYPE_TEXT);

        return [$nodeName, $nodeType];
    }

    public function isCheckable($element): bool
    {
        return
            $element instanceof UssElement &&
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
        return
            $element instanceof UssElement && (
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

    public function simplifyContent(string $content, string $breaker = ' '): ?string
    {
        $content = str_replace(['-', '[', ']', ' '], $breaker, $content);
        return trim(strtolower($content), $breaker);
    }
}
