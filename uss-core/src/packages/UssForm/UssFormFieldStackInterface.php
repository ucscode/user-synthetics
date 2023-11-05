<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

interface UssFormFieldStackInterface
{
    public function push(UssFormField $field): self;
    public function get(int $index): ?UssFormField;
    public function getAll(): array;
    public function pull(int|UssFormField $indexField): ?UssFormField;
    
    public function getFieldsetElement(): UssElement;
    public function setFieldsetAttribute(string $name, string $value, bool $append): self;
    public function getFieldsetAttribute(string $name): ?string;
    public function removeFieldsetAttribute(string $name, ?string $detach): self;

    public function getLegendElement(): UssElement;
    public function setLegendAttribute(string $name, string $value, bool $append): self;
    public function getLegendAttribute(string $name): ?string;
    public function removeLegendAttribute(string $name, ?string $detach): self;
    public function setLegendValue(?string $value): self;
    public function getLegendValue(): ?string;

    public function getStackContainerElement(): UssElement;
    public function setStackContainerAttribute(string $name, string $value, bool $append): self;
    public function getStackContainerAttribute(string $name): ?string;
    public function removeStackContainerAttribute(string $name, ?string $detach): self;

    public function setFieldsetDisabled(bool $status): self;
    public function isFieldsetDisabled(): bool;
    public function getStackAsElement(): UssElement;
    public function getStackAsHTML(): string;
}