<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;

trait FieldWidgetContainerTrait
{
    protected array $widgetContainer = [
        'element' => null,
    ];

    public function getWidgetContainerElement(): UssElement
    {
        return $this->widgetContainer['element'];
    }

    public function setWidgetContainerAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->widgetContainer['element'], $name, $value, $append);
    }

    public function getWidgetContainerAttribute(string $name): ?string
    {
        return $this->widgetContainer['element']->getAttribute($name);
    }

    public function removeWidgetContainerAttribute(string $name, ?string $detach): self
    {
        $method = !is_null($detach) ? 'removeAttributeValue' : 'removeAttribute';
        $this->widgetContainer['element']->{$method}($name, $detach);
        return $this;
    }
}
