<?php

interface UssInterface {
    
    public function pathToUrl(string $pathname, bool $hidebase = false): string;

    public function render(string $templatePath, array $variables, ?UssTwigBlockManager $blockManager = null): void;

    public function keygen($length = 10, bool $use_special_char = false): string;

    public function replaceVar(string $string, array $data): string;

    public function isAbsolutePath(string $path): bool;

}