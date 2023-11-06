<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElement;

interface UssFormFieldInterface
{
    public const VALIDATION_ERROR = 'invalid';
    public const VALIDATION_SUCCESS = 'valid';
    
    public function getFieldAsHTML(): string;
    public function getFieldAsElement(): UssElement;

    /*
    public function getXXXElement(): UssElement;
    public function setXXXAttribute(string $name, string $value): self;
    public function getXXXAttribute(string $name): string;
    public function removeXXXAttribute(string $name): self;
    public function setXXXValue(null|string|UssElement $value): self;
    public function getXXXValue(): null|string|UssElement;
    */

    /**
     * For: Row
     */
    public function getRowElement(): UssElement;
    public function setRowAttribute(string $name, string $value, bool $append): self;
    public function getRowAttribute(string $name): ?string;
    public function removeRowAttribute(string $name, ?string $detach): self;

    /**
     * For: Container
     */
    public function getContainerElement(): UssElement;
    public function setContainerAttribute(string $name, string $value, bool $append): self;
    public function getContainerAttribute(string $name): ?string;
    public function removeContainerAttribute(string $name, ?string $detach): self;

    /**
     * For: Info
     */
    public function getInfoElement(): UssElement;
    public function setInfoAttribute(string $name, string $value, bool $append): self;
    public function getInfoAttribute(string $name): ?string;
    public function removeInfoAttribute(string $name, ?string $detach): self;
    public function setInfoMessage(null|string|UssElement $value, ?string $icon): self;
    public function getInfoMessage(): null|string|UssElement;
    public function setInfoHidden(bool $status): self;
    public function isInfoHidden(): bool;

    /**
     * For: Label
     */
    public function getLabelElement(): UssElement;
    public function setLabelAttribute(string $name, string $value, bool $append): self;
    public function getLabelAttribute(string $name): ?string;
    public function removeLabelAttribute(string $name, ?string $detach): self;
    public function setLabelValue(null|string|UssElement $value): self;
    public function getLabelValue(): null|string|UssElement;
    public function setLabelHidden(bool $status): self;
    public function isLabelHidden(): bool;

    /**
     * For: Error
     */
    public function getValidationElement(): UssElement;
    public function setValidationAttribute(string $name, string $value, bool $append): self;
    public function getValidationAttribute(string $name): ?string;
    public function removeValidationAttribute(string $name, ?string $detach): self;
    public function setValidationType(?string $validationType): self;
    public function getValidationType(): ?string;
    public function setValidationMessage(?string $message, ?string $icon): self;
    public function getValidationMessage(): ?string;
    public function setValidationHidden(bool $status): self;
    public function isValidationHidden(): bool;

    /**
     * For: WidgetContainer
     */
    public function getWidgetContainerElement(): UssElement;
    public function setWidgetContainerAttribute(string $name, string $value, bool $append): self;
    public function getWidgetContainerAttribute(string $name): ?string;
    public function removeWidgetContainerAttribute(string $name, ?string $detach): self;

    /**
     * For: Widget
     */
    public function getWidgetElement(): UssElement;
    public function setWidgetAttribute(string $name, string $value, bool $append): self;
    public function getWidgetAttribute(string $name): ?string;
    public function removeWidgetAttribute(string $name, ?string $detach): self;
    public function setWidgetValue(?string $value): self;
    public function getWidgetValue(): ?string;
    public function appendToWidget(null|string|UssElement $appendant): self;
    public function getWidgetAppendant(): ?UssElement;
    public function prependToWidget(null|string|UssElement $prependant): self;
    public function getWidgetPrependant(): ?UssElement;

    /**
     * For: Select Widget
     */
    public function setWidgetOptions(array $options): self;
    public function setWidgetOption(string $key, string $displayValue): self;
    public function getWidgetOptions(): array;
    public function getWidgetOptionElement(string $key): ?UssElement;
    public function removeWidgetOption(string $key): self;
    public function hasWidgetOption(string $key): bool;

    /**
     * For: Checkable Widget
     */
    public function isCheckable(): bool;
    public function isButton(): bool;
    public function setWidgetChecked(bool $status): self;
    public function isWidgetChecked(): bool;
    public function isHiddenWidget(): bool;
    public function setDisabled(bool $status): self;
    public function isDisabled(): bool;
    public function setReadonly(bool $status): self;
    public function isReadonly(): bool;
    public function setRequired(bool $status): self;
    public function isRequired(): bool;
}
