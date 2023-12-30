<?php

namespace Ucscode\UssForm\Resource\Context;

use ReflectionClass;
use ReflectionProperty;

abstract class AbstractElementContext
{
    abstract protected function groupContextElements(): void;

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
