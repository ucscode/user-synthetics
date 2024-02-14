<?php

namespace Uss\Component\Kernel\Extension;

use Uss\Component\Block\BlockManager;
use Uss\Component\Kernel\Interface\UssInterface;

class ExtensionAppObject
{
    public function __construct(protected UssInterface $framework)
    {}

    # Get an option
    public function getOption(string $name): mixed
    {
        return $this->framework->options->get($name);
    }

    # Get dirname
    public function getDirname(string $path, int $level = 1): string 
    {
        return dirname($path, $level);
    }

    # Render Inline Blocks
    public function renderBlocks(string $blockName, array $_context): ?string
    {
        $outputs = [];

        if($block = BlockManager::instance()->getBlock($blockName)) {
            
            $templates = $block->getTemplates();
            uasort($templates, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());

            foreach($templates as $name => $blockTemplate) {
                if(!$blockTemplate->isRendered()) {
                    $blockTemplate->fulfilled();
                    $outputs[] = $this->framework->twig
                        ->resolveTemplate($blockTemplate->getTemplate())
                        ->render($blockTemplate->getContext() + $_context);
                    continue;
                }
                $outputs[] = sprintf("<!-- * WARNING: Isolated template already fulfilled => [%s].%s -->", $blockName, $name);
            }

            $contents = $block->getContents(); 
            uasort($contents, fn ($a, $b) => $a['priority'] <=> $b['priority']);

            foreach($contents as $content) {
                $outputs[] = $content['content'];
            }
        }

        return implode("\n", $outputs);
    }

    public function __debugInfo()
    {
        return array_filter(get_class_methods($this), function($method) {
            return strpos($method, '__') !== 0;
        });
    }
}