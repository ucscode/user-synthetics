<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface WidgetContainerInterface
{
    public function getWidgetContainerElement(): UssElement;
    public function setWidgetContainerAttribute(string $name, string $value, bool $append): self;
    public function getWidgetContainerAttribute(string $name): ?string;
    public function removeWidgetContainerAttribute(string $name, ?string $detach): self;
}
