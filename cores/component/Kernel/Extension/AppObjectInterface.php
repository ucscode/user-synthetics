<?php

namespace Uss\Component\Kernel\Extension;

interface AppObjectInterface
{
    public function getOption(string $name): mixed;
    public function getDirname(string $path, int $level = 1): string;
    public function renderBlocks(string $blockName, array $_context): ?string;
    public function removeSystemBlockContent(string $blockName, string $resourceName): void;
    public function jsonEncode(mixed $context): ?string;
    public function jsonDecode(string $json): ?array;
    public function base64Encode(string $string): string;
    public function base64Decode(string $string, bool $strict): mixed;
}