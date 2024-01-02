<?php

namespace Uss\Component\Kernel\Interface;

interface UssInterface
{
    public const NAMESPACE = 'Uss';
    public const SESSION_KEY = 'UssId'; // (PHP Session)
    public const CLIENT_KEY = 'uss_client_id'; // (Browser Cookie)

    public function terminate(bool|int|null $status, ?string $message, array $data = []): void;
    public function filterContext(string|array $path, string $divider = '/'): string;
    public function pathToUrl(string $pathname, bool $hidebase = false): string;
    public function keygen(int $length, bool $use_special_char): string;
    public function isAbsolutePath(string $path): bool;
    public function render(string $templatePath, array $variables): ?string;
    public function getUrlSegments(?int $index = null): array|string|null;
}
