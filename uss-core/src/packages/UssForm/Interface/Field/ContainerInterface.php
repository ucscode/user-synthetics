<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface ContainerInterface
{
    public function getContainerElement(): UssElement;
    public function setContainerAttribute(string $name, ?string $value, bool $append): self;
    public function getContainerAttribute(string $name): ?string;
    public function removeContainerAttribute(string $name, ?string $detach): self;
}
