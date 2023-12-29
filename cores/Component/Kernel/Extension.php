<?php

namespace Uss\Component\Kernel;

use Uss\Component\Manager\BlockManager;

/**
 * This extension is a minified version of Uss class for twig
 * It provides only limited properties and methods from the Uss class to the twig template
 */
final class Extension
{
    public readonly string $jsCollectionEncoded;

    public function __construct(private Uss $uss)
    {
        $this->uss->jsCollection['platform'] = UssImmutable::PROJECT_NAME;
        $this->uss->jsCollection['url'] = $this->uss->pathToUrl(ROOT_DIR);
        $this->jsCollectionEncoded = base64_encode(json_encode($this->uss->jsCollection));
        $this->uss->twigContext['favicon'] ??= $this->uss->twigContext['page_icon'];
    }

    /**
     * Conver absolute path to Url
     */
    public function pathToUrl(string $path, bool $base = false): string
    {
        return $this->uss->pathToUrl($path, $base);
    }

    /**
     * Generate random unique character
     */
    public function keygen(int $length = 10, bool $use_spec_chars = false): string
    {
        return $this->uss->keygen($length, $use_spec_chars);
    }

    /**
     * Convert time to elapse string
     */
    public function relativeTime($time, bool $full = false): string
    {
        return $this->uss->relativeTime($time, $full);
    }

    /**
     * Call a function within twig
     */
    public function call_user_func(string|array $callback, ...$args): mixed
    {
        return call_user_func($callback, ...$args);
    }

    /**
     * Self Methods
     */
    public function renderBlocks(string $name, int $indent = 1): ?string
    {
        $blockManager = BlockManager::instance();
        $blocks = $blockManager->getBlocks($name);
        if(is_array($blocks)) {
            $indent = str_repeat("\t", abs($indent));
            return implode("\n{$indent}", $blocks);
        };
        return null;
    }

    # Get an option
    public function getOption(string $name): mixed
    {
        return $this->uss->options->get($name);
    }

}
