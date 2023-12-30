<?php

namespace Ucscode\UssForm\Interface;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\Field\ContainerInterface;
use Ucscode\UssForm\Interface\Field\InfoInterface;
use Ucscode\UssForm\Interface\Field\LabelInterface;
use Ucscode\UssForm\Interface\Field\RowInterface;
use Ucscode\UssForm\Interface\Field\ValidationInterface;
use Ucscode\UssForm\Interface\Field\WidgetContainerInterface;
use Ucscode\UssForm\Interface\Field\WidgetInterface;
use Ucscode\UssForm\Interface\Field\WidgetMinorInterface;

interface UssFormFieldInterface extends ContainerInterface, InfoInterface, LabelInterface, RowInterface, ValidationInterface, WidgetContainerInterface, WidgetInterface, WidgetMinorInterface
{
    public function getFieldAsHTML(): string;
    public function getFieldAsElement(): UssElement;
    public function addLineBreak(): self;
    public function removeLineBreak(): self;
    public function hasLineBreak(): bool;
    public function getLineBreak(): ?UssElement;
}
