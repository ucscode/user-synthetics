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

interface UssFormFieldInterface extends ContainerInterface, InfoInterface, LabelInterface, RowInterface, ValidationInterface, WidgetContainerInterface, WidgetInterface
{
    public function getFieldAsHTML(): string;
    public function getFieldAsElement(): UssElement;
}
