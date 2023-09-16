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

    public function getFieldset(string $name): ?array;

    public function populate(array $data): void;

    public function getValue(UssElement $node): ?string;

    public function setValue(UssElement $node, $value, bool $overwrite): bool;

    public function appendField(UssElement $column): ?UssElement;

    public function addDetail(string $key, $value): bool;

    public function getDetail(string $key): mixed;

    public function removeDetail(string $key): void;

}
