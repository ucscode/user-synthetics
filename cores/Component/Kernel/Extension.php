<?php

namespace Uss\Component\Kernel;

use Twig\TemplateWrapper;
use Uss\Component\Block\BlockManager;
use Uss\Component\Block\BlockTemplate;

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
     * Self Methods
     */
    public function renderBlockContents(string $name): ?string
    {
        $outputs = [];

        if($block = BlockManager::instance()->getBlock($name)) {
            // Render Templates First
            $templates = $block->getTemplates();
            usort($templates, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());
            array_walk($templates, function(BlockTemplate $blockTemplate) use (&$outputs) {
                $outputs[] = $this->uss->twigEnvironment
                    ->resolveTemplate($blockTemplate->getTemplate())
                    ->render($blockTemplate->getContext());
            });

            // Render Contents Next;
            $contents = $block->getContents();
            usort($contents, fn ($a, $b) => $a['priority'] <=> $b['priority']);
            array_walk($contents, function(array $content) use (&$outputs) {
                $outputs[] = $content['content'];
            });
        }

        return implode("\n", $outputs);
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

    # Get an option
    public function getOption(string $name): mixed
    {
        return $this->uss->options->get($name);
    }

}
