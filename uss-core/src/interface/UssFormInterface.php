<?php

interface UssFormInterface extends UssElementInterface
{
    public function add(
        string $name,
        string $fieldType,
        string|array|null $context,
        array $config
    ): UssElementBuilder;

    public function addRow(string $class): UssElementBuilder;

}
