<?php

namespace Ucscode\UssForm\Interface;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\Field\LabelInterface;
use Ucscode\UssForm\Interface\Field\WidgetContainerInterface;
use Ucscode\UssForm\Interface\Field\WidgetInterface;

interface UssFormFieldSecondaryInterface extends LabelInterface, WidgetContainerInterface, WidgetInterface
{
    public function getFieldAsHTML(): string;
    public function getFieldAsElement(): UssElement;
}
