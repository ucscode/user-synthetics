<?php

interface UssElementInterface
{
    public function isVoid(bool $void): self;

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

    public function appendChild(UssElementBuilder $child): void;

    public function prependChild(UssElementBuilder $child): void;

    public function insertBefore(UssElementBuilder $child, UssElementBuilder $refNode): void;

    public function insertAfter(UssElementBuilder $child, UssElementBuilder $refNode): void;

    public function replaceChild(UssElementBuilder $child, UssElementBuilder $refNode): void;

    public function firstChild(): ?UssElementBuilder;

    public function lastChild(): ?UssElementBuilder;

    public function getChild(int $index): ?UssElementBuilder;

    public function removeChild(UssElementBuilder $child): void;

    public function getHTML(bool $indent = false): string;

}
