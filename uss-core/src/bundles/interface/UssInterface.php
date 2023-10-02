<?php

interface UssInterface {
    
    public function addTwigFilesystem(string $directory, string $namespace): void;

    public function addTwigExtension(string $fullyQualifiedClassName): void;

    public function addJsProperty(string $key, mixed $value): void;

    public function getJsProperty(?string $key = null): mixed;

    public function removeJsProperty(string $key): mixed;

    public function getRouteInventory(bool $authentic = false): array;

    public function exit(bool|int|null $status, ?string $message, array $data): void;

    public function die(bool|int|null $status, ?string $message, array $data): void;

    public function filterContext(string|array $path, string $divider = '/'): string;

    public function getUrl(string $pathname, bool $hidebase = false): string;

    public function render(string $templatePath, array $variables): void;

    public function keygen($length = 10, bool $use_special_char = false): string;

    public function replaceVar(string $string, array $data): string;

    public function isAbsolutePath(string $path): bool;

}