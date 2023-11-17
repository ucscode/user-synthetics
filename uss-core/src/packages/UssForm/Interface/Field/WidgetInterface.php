<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssFormFieldSecondary;

interface WidgetInterface
{
    public function getWidgetElement(): UssElement;

    public function setWidgetAttribute(string $name, ?string $value, bool $append): self;
    public function getWidgetAttribute(string $name): ?string;
    public function removeWidgetAttribute(string $name, ?string $detach): self;

    public function setWidgetValue(?string $value): self;
    public function getWidgetValue(): ?string;

    public function isCheckable(): bool;
    public function isButton(): bool;
    public function isWidgetHidden(): bool;

    public function setWidgetChecked(bool $status): self;
    public function isWidgetChecked(): bool;

    public function setDisabled(bool $status): self;
    public function isDisabled(): bool;

    public function setReadonly(bool $status): self;
    public function isReadonly(): bool;

    public function setRequired(bool $status): self;
    public function isRequired(): bool;

    public function createSecondaryField(string $name, string $type): UssFormFieldSecondary;
    public function getSecondaryField(string $name): ?UssFormFieldSecondary;
    public function removeSecondaryField(string $name): ?UssFormFieldSecondary;
    public function getSecondaryFields(): array;
}
