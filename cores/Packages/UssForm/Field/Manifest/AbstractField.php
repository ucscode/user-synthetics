<?php

namespace Ucscode\UssForm\Field\Manifest;

use ReflectionClass;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\ElementContext;

abstract class AbstractField implements FieldInterface
{
    public readonly string $nodeName;
    public readonly ?string $nodeType;
    protected ElementContext $elementContext;

    public function __construct(string $nodeName = Field::NODE_INPUT, ?string $nodeType = Field::TYPE_TEXT)
    {
        [$this->nodeName, $this->nodeType] = $this->controlPrototype($nodeName, $nodeType);
        $this->elementContext = new ElementContext($this);
    }

    protected function controlPrototype(string $nodeName, string $nodeType): array
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
}
