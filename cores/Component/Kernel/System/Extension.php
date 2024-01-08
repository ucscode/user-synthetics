<?php

namespace Uss\Component\Kernel\System;

use Uss\Component\Kernel\Abstract\AbstractExtension;
use Uss\Component\Kernel\Resource\Enumerator;

/**
 * This extension is a minified version of Uss class for twig
 * It provides only limited properties and methods from the Uss class to the twig template
 */
final class Extension extends AbstractExtension
{
    /**
     * Conver absolute path to Url
     */
    public function pathToUrl(string $path, bool $base = false): string
    {
        return $this->system->pathToUrl($path, $base);
    }

    /**
     * Generate random unique character
     */
    public function keygen(int $length = 10, bool $use_spec_chars = false): string
    {
        return $this->system->keygen($length, $use_spec_chars);
    }

    /**
     * Get Twig Scope
     */
    public function getTemplateSchema(string $template, Enumerator $enum = Enumerator::URL, int $index = 0): string
    {
        return $this->system->getTemplateSchema($template, $enum, $index);
    }

    /**
     * Convert time to elapse string
     */
    public function relativeTime($time, bool $full = false): string
    {
        return $this->system->relativeTime($time, $full);
    }
}
