<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElementNodeListInterface;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Widget\Widget;

interface FieldInterface extends FieldTypesInterface, UssElementNodeListInterface
{
    public function getElementContext(): ElementContext;
    public function addWidget(string $name, Widget $widget): self;
    public function getWidget(string $name): Widget;
    public function hasWidget(string|Widget $widget): bool;
    public function getWidgets(): array;
}
