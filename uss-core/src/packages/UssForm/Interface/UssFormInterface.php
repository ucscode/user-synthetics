<?php

namespace Ucscode\UssForm\Interface;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Internal\UssFormFieldStack;
use Ucscode\UssForm\UssFormField;

interface UssFormInterface
{
    public const TYPE_TEXT = 'text';
    public const TYPE_NUMBER = 'number';
    public const TYPE_DATE = 'date';
    public const TYPE_TIME = 'time';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_RADIO = 'radio';
    public const TYPE_SWITCH = 'switch';
    public const TYPE_FILE = 'file';
    public const TYPE_COLOR = 'color';
    public const TYPE_RANGE = 'range';
    public const TYPE_SEARCH = 'search';
    public const TYPE_URL = 'url';
    public const TYPE_TEL = 'tel';
    public const TYPE_HIDDEN = 'hidden';
    public const TYPE_SUBMIT = 'submit';
    public const TYPE_BUTTON = 'button';
    public const TYPE_RESET = 'reset';
    public const TYPE_DATETIME_LOCAL = 'datetime-local';

    public function addFieldStack(?string $name, bool $useFieldset): UssFormFieldStack;
    public function getFieldStack(string $name): ?UssFormFieldStack;
    public function removeFieldStack(string $name): self;
    public function getFieldStacks(): array;
    
    public function getFieldStackByField(string $name): ?UssFormFieldStack;
    public function getFieldStackByElement(string $name): ?UssFormFieldStack;

    public function addField(string $name, UssFormField $field, array $options): self;
    public function getField(string $name): ?UssFormField;
    public function removeField(string $name): self;
    public function getFields(): array;

    public function addCustomElement(string $name, UssElement $element): self;
    public function getCustomElement(string $name): ?UssElement;
    public function removeCustomElement(string $name): self;

    public function populate(array $data): self;
}
