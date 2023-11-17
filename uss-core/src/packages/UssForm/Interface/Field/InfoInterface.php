<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface InfoInterface
{
    public function getInfoElement(): UssElement;

    public function setInfoAttribute(string $name, ?string $value, bool $append): self;
    public function getInfoAttribute(string $name): ?string;
    public function removeInfoAttribute(string $name, ?string $detach): self;

    public function setInfoMessage(null|string|UssElement $value, ?string $icon): self;
    public function getInfoMessage(): null|string|UssElement;

    public function setInfoHidden(bool $status): self;
    public function isInfoHidden(): bool;
}
