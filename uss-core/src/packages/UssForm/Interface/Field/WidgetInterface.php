<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface WidgetInterface
{
    public function getWidgetElement(): UssElement;
    public function setWidgetAttribute(string $name, ?string $value, bool $append): self;
    public function getWidgetAttribute(string $name): ?string;
    public function removeWidgetAttribute(string $name, ?string $detach): self;
    public function setWidgetValue(?string $value): self;
    public function getWidgetValue(): ?string;
    public function appendToWidget(null|string|UssElement $appendant): self;
    public function getWidgetAppendant(): ?UssElement;
    public function prependToWidget(null|string|UssElement $prependant): self;
    public function getWidgetPrependant(): ?UssElement;

    public function setWidgetOptions(array $options): self;
    public function setWidgetOption(string $key, string $displayValue): self;
    public function getWidgetOptions(): array;
    public function getWidgetOptionElement(string $key): ?UssElement;
    public function removeWidgetOption(string $key): self;
    public function hasWidgetOption(string $key): bool;

    public function isCheckable(): bool;
    public function isButton(): bool;
    public function setWidgetChecked(bool $status): self;
    public function isWidgetChecked(): bool;
    public function isWidgetHidden(): bool;
    public function setDisabled(bool $status): self;
    public function isDisabled(): bool;
    public function setReadonly(bool $status): self;
    public function isReadonly(): bool;
    public function setRequired(bool $status): self;
    public function isRequired(): bool;

    public function createAlt(string $name, string $type): UssElement;
    public function getAlt(string $name): ?UssElement;
    public function removeAlt(string $name): ?UssElement;
    public function getAlts(): array;
}
