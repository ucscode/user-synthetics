<?php

namespace Ucscode\UssElement;

interface UssElementInterface
{
    public function setVoid(bool $void): self;

    public function setAttribute(string $attr, ?string $value = null): self;

    public function getAttribute(string $attr): ?string;

    public function hasAttribute(string $attr): bool;

    public function removeAttribute(string $attr): self;

    public function addAttributeValue(string $attr, string $value): self;

    public function removeAttributeValue(string $attr, string $value): self;

    public function hasAttributeValue(string $attr, string $value): bool;

    public function setContent(string $content): self;

    public function getContent(): ?string;

    public function hasContent(): bool;

    // Child Management

    public function reset(): void;

    public function appendChild(UssElement $child): void;

    public function prependChild(UssElement $child): void;

    public function insertBefore(UssElement $child, UssElement $refNode): void;

    public function insertAfter(UssElement $child, UssElement $refNode): void;

    public function replaceChild(UssElement $child, UssElement $refNode): void;

    public function firstChild(): ?UssElement;

    public function lastChild(): ?UssElement;

    public function getChild(int $index): ?UssElement;

    public function removeChild(UssElement $child): void;

    public function getHTML(bool $indent = false): string;

}
