<?php

interface UssInterface
{
    public const SANITIZE_ENTITIES = 1;
    public const SANITIZE_SQL = 2;
    public const SANITIZE_SCRIPT_TAGS = 4;

    public function addTwigFilesystem(string $directory, string $namespace): void;
    public function addTwigExtension(string $fullyQualifiedClassName): void;
    public function addJsProperty(string $key, mixed $value): void;
    public function getJsProperty(?string $key = null): mixed;
    public function removeJsProperty(string $key): mixed;
    public function exit(bool|int|null $status, ?string $message, array $data): void;
    public function die(bool|int|null $status, ?string $message, array $data): void;
    public function filterContext(string|array $path, string $divider = '/'): string;
    public function abspathToUrl(string $pathname, bool $hidebase = false): string;
    public function render(string $templatePath, array $variables): void;
    public function keygen(int $length, bool $use_special_char): string;
    public function replaceVar(string $string, array $data): string;
    public function isAbsolutePath(string $path): bool;
}
