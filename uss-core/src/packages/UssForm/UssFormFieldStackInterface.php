<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

interface UssFormFieldStackInterface
{
    public function addField(string $name, UssFormField $field): self;
    public function addElement(string $name, UssElement $element): self;
    public function getField(string $name): ?UssFormField;
    public function getElement(string $name): ?UssElement;
    public function removeField(string $name): self;
    public function removeElement(string $name): self;
    public function getFields(): array;
    public function getElements(): array;

    public function getOuterContainerElement(): UssElement;
    public function setOuterContainerAttribute(string $name, string $value, bool $append): self;
    public function getOuterContainerAttribute(string $name): ?string;
    public function removeOuterContainerAttribute(string $name, ?string $detach): self;
    public function setOuterContainerAsDIV(bool $status): self;
    public function isOuterContainerDIV(): bool;

    public function getTitleElement(): UssElement;
    public function setTitleAttribute(string $name, string $value, bool $append): self;
    public function getTitleAttribute(string $name): ?string;
    public function removeTitleAttribute(string $name, ?string $detach): self;
    public function setTitleValue(?string $value): self;
    public function getTitleValue(): ?string;
    public function hideTitle(bool $status): self;
    public function isHiddenTitle(): bool;

    public function getSubtitleElement(): UssElement;
    public function setSubtitleAttribute(string $name, string $value, bool $append): self;
    public function getSubtitleAttribute(string $name): ?string;
    public function removeSubtitleAttribute(string $name, ?string $detach): self;
    public function setSubtitleValue(?string $value): self;
    public function getSubtitleValue(): ?string;
    public function hideSubtitle(bool $status): self;
    public function isHiddenSubtitle(): bool;

    public function getInstructionElement(): UssElement;
    public function setInstructionAttribute(string $name, string $value, bool $append): self;
    public function getInstructionAttribute(string $name): ?string;
    public function removeInstructionAttribute(string $name, ?string $detach): self;
    public function setInstructionValue(string|null|UssElement $value): self;
    public function getInstructionValue(): UssElement|string|null;
    public function hideInstruction(bool $status): self;
    public function isHiddenInstruction(): bool;

    public function getInnerContainerElement(): UssElement;
    public function setInnerContainerAttribute(string $name, string $value, bool $append): self;
    public function getInnerContainerAttribute(string $name): ?string;
    public function removeInnerContainerAttribute(string $name, ?string $detach): self;

    public function setFieldStackDisabled(bool $status): self;
    public function isFieldStackDisabled(): bool;
    public function getFieldStackAsElement(): UssElement;
    public function getFieldStackAsHTML(): string;
}
