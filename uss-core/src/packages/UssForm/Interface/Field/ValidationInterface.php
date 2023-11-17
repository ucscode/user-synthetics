<?php

namespace Ucscode\UssForm\Interface\Field;

use Ucscode\UssElement\UssElement;

interface ValidationInterface
{
    public const VALIDATION_ERROR = 'invalid';
    public const VALIDATION_SUCCESS = 'valid';

    public function getValidationElement(): UssElement;
    public function setValidationAttribute(string $name, ?string $value, bool $append): self;
    public function getValidationAttribute(string $name): ?string;
    public function removeValidationAttribute(string $name, ?string $detach): self;
    public function setValidationType(?string $validationType): self;
    public function getValidationType(): ?string;
    public function setValidationMessage(?string $message): self;
    public function getValidationMessage(): ?string;
    public function setValidationHidden(bool $status): self;
    public function isValidationHidden(): bool;
}
