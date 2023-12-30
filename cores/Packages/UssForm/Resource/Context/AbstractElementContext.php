<?php

namespace Ucscode\UssForm\Resource\Context;

use ReflectionClass;
use ReflectionProperty;

abstract class AbstractElementContext
{
    abstract public function export(): string;
    abstract public function visualizeContextElements(): void;
    abstract protected function assembleContextElements(): void;

    protected function getContextElements(): array
    {
        $elements = [];
        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_READONLY);
        foreach($properties as $property) {
            $name = $property->getName();
            $context = $this->{$name};
            if($context instanceof AbstractContext) {
                $elements[$name] = $context->getElement();
            }
        };
        return $elements;
    }
}
