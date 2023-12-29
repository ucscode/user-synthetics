<?php

namespace Uss\Component\Kernel;

interface UssInterface
{
    public const NAMESPACE = 'Uss';

    public function terminate(bool|int|null $status, ?string $message, array $data = []): void;
    public function filterContext(string|array $path, string $divider = '/'): string;
    public function pathToUrl(string $pathname, bool $hidebase = false): string;
    public function keygen(int $length, bool $use_special_char): string;
    public function isAbsolutePath(string $path): bool;
    public function render(string $templatePath, array $variables): ?string;
    public function getUrlSegments(?int $index = null): array|string|null;
}
