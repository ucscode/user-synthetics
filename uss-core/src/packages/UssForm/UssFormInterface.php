<?php

namespace Ucscode\UssForm;

use Ucscode\UssElement\UssElementInterface;
use Ucscode\UssElement\UssElement;

interface UssFormInterface
{
    public function addFieldStack(string $name, ?UssFormFieldStack $fieldStack): self;
    public function getFieldStack(string $name): ?UssFormFieldStack;

    public function addField(string $name, UssFormField $field, ?string $fieldStackName): self;
    public function getField(string $name): ?UssFormField;

    public function addCustomElement(string $name, UssElement $element, ?string $fieldStackName): self;
    public function getCustomElement(string $name): ?UssElement;

    public function populate(array $data): self;
}
