<?php

namespace Ucscode\UssForm\Field;

use Ucscode\UssForm\Field\Manifest\AbstractField;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Widget\Widget;

class Field extends AbstractField
{
    protected array $widgets = [];

    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }

    public function addWidget(string $name, Widget $widget): self
    {
        $this->widgets[$name] = $widget;
        return $this;
    }

    public function getWidget(string $name): Widget
    {
        return $this->widgets[$name] ?? null;
    }

    public function hasWidget(string|Widget $widget): bool
    {
        return false;
    }

    public function getWidgets(): array
    {
        return $this->widgets;
    }
}
