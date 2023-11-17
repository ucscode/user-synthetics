<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface WidgetMinorInterface
{
    public function setWidgetSuffix(null|string|UssElement $appendant): self;
    public function getWidgetSuffix(): ?UssElement;
    public function removeWidgetSuffix(): self;

    public function setWidgetPrefix(null|string|UssElement $prependant): self;
    public function getWidgetPrefix(): ?UssElement;
    public function removeWidgetPrefix(): self;

    public function setWidgetOptions(array $options): self;
    public function setWidgetOption(string $key, string $displayValue): self;
    public function getWidgetOptions(): array;
    public function getWidgetOptionElement(string $key): ?UssElement;
    public function removeWidgetOption(string $key): self;
    public function hasWidgetOption(string $key): bool;
}
