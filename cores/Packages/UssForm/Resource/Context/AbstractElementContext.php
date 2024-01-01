<?php

namespace Ucscode\UssForm\Resource\Context;

use ReflectionClass;
use ReflectionProperty;

abstract class AbstractElementContext
{
    abstract public function export(): string;
    abstract public function visualizeContextElements(): void;
    abstract protected function assembleContextElements(): void;

    protected bool $fixed = false;

    protected function getContextElements(): array
    {
        $elements = [];
        foreach($this->getAllContext() as $name => $context) {
            $elements[$name] = $context->getElement();
        }
        return $elements;
    }

    protected function getAllContext(): array
    {
        $contexts = [];
        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_READONLY);
        foreach($properties as $property) {
            $name = $property->getName();
            $context = $this->{$name};
            if($context instanceof AbstractContext) {
                $contexts[$name] = $context;
            }
        };
        return $contexts;
    }

    public function setFixed(bool $status = true): self
    {
        $this->fixed = $status;
        return $this;
    }

    public function isFixed(): bool
    {
        return $this->fixed;
    }
}
