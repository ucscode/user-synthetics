<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElementInterface;
use Ucscode\UssElement\UssElement;

interface UssFormInterface extends UssElementInterface
{
    public function add(
        string $name,
        string $fieldType,
        string|array|null $context,
        array $config
    ): UssElement;

    public function addRow(string $class): UssElement;

}
