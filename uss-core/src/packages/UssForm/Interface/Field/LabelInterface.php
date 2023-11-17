<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface LabelInterface
{
    public function getLabelElement(): UssElement;
    public function setLabelAttribute(string $name, ?string $value, bool $append): self;
    public function getLabelAttribute(string $name): ?string;
    public function removeLabelAttribute(string $name, ?string $detach): self;
    public function setLabelValue(null|string|UssElement $value): self;
    public function getLabelValue(): null|string|UssElement;
    public function setLabelHidden(bool $status): self;
    public function isLabelHidden(): bool;
}
