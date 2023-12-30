<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElementNodeListInterface;
use Ucscode\UssForm\Field\Manifest\ElementContext;

interface FieldInterface extends FieldTypesInterface, UssElementNodeListInterface
{
    public function getElementContext(): ElementContext;
}
