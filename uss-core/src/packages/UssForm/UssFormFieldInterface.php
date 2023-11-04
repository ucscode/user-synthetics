<?php

use Ucscode\UssElement\UssElement;

interface UssFormFieldInterface
{
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
    public function setRowAttribute(string $name, string $value): self;
    public function getRowAttribute(string $name): ?string;
    public function removeRowAttribute(string $name): self;

    /**
     * For: Container
     */
    public function getContainerElement(): UssElement;
    public function setContainerAttribute(string $name, string $value): self;
    public function getContainerAttribute(string $name): ?string;
    public function removeContainerAttribute(string $name): self;

    /**
     * For: Info
     */
    public function getInfoElement(): UssElement;
    public function setInfoAttribute(string $name, string $value): self;
    public function getInfoAttribute(string $name): ?string;
    public function removeInfoAttribute(string $name): self;
    public function setInfoValue(null|string|UssElement $value): self;
    public function getInfoValue(): null|string|UssElement;

    /**
     * For: Label
     */
    public function getLabelElement(): UssElement;
    public function setLabelAttribute(string $name, string $value): self;
    public function getLabelAttribute(string $name): ?string;
    public function removeLabelAttribute(string $name): self;
    public function setLabelValue(null|string|UssElement $value): self;
    public function getLabelValue(): null|string|UssElement;

    /**
     * For: Error
     */
    public function getErrorElement(): UssElement;
    public function setErrorAttribute(string $name, string $value): self;
    public function getErrorAttribute(string $name): ?string;
    public function removeErrorAttribute(string $name): self;
    public function setErrorValue(null|string|UssElement $value): self;
    public function getErrorValue(): null|string|UssElement;

    /**
     * For: WidgetContainer
     */
    public function getWidgetContainerElement(): UssElement;
    public function setWidgetContainerAttribute(string $name, string $value): self;
    public function getWidgetContainerAttribute(string $name): ?string;
    public function removeWidgetContainerAttribute(string $name): self;

    /**
     * For: Widget
     */
    public function getWidgetElement(): UssElement;
    public function setWidgetAttribute(string $name, string $value): self;
    public function getWidgetAttribute(string $name): ?string;
    public function removeWidgetAttribute(string $name): self;
    public function setWidgetValue(?string $value): self;
    public function getWidgetValue(): ?string;
    public function setWidgetAppendant(null|string|UssElement $appendant): self;
    public function getWidgetAppendant(): null|string|UssElement;
    public function setWidgetPrependant(null|string|UssElement $prependant): self;
    public function getWidgetPrependant(): null|string|UssElement;

    /**
     * For: Widget Modifier
     */
    public function setWidgetOptions(array $options): self;
    public function setWidgetOption(string $key, string $displayValue): self;
    public function removeWidgetOption(string $key): self;
    public function hasWidgetOption(string $key): bool;
    public function getWidgetOptions(): array;
}