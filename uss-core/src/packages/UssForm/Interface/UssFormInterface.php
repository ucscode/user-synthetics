<?php

namespace Ucscode\UssForm\Interface;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\UssFormFieldStack;
use Ucscode\UssForm\UssFormField;

interface UssFormInterface
{
    public function addFieldStack(?string $name, ?UssFormFieldStack $fieldStack): UssFormFieldStack;
    public function getFieldStack(string $name): ?UssFormFieldStack;

    public function addField(string $name, UssFormField $field, array $options): self;
    public function getField(string $name): ?UssFormField;

    public function addCustomElement(string $name, UssElement $element, array $options): self;
    public function getCustomElement(string $name): ?UssElement;

    public function populate(array $data): self;
}
