<?php

namespace Uss\Component\Kernel\Abstract;

use Uss\Component\Block\BlockManager;
use Uss\Component\Block\BlockTemplate;
use Uss\Component\Kernel\Enumerator;
use Uss\Component\Kernel\Uss;
use Uss\Component\Kernel\UssImmutable;

abstract class AbstractInternalExtension
{
    public readonly string $jsCollectionEncoded;
    public readonly array $ENUM;

    public function __construct(protected Uss $uss)
    {
        $this->uss->jsCollection['platform'] = UssImmutable::PROJECT_NAME;
        $this->uss->jsCollection['url'] = $this->uss->pathToUrl(ROOT_DIR);
        $this->jsCollectionEncoded = base64_encode(json_encode($this->uss->jsCollection));
        $this->uss->twigContext['favicon'] ??= $this->uss->twigContext['page_icon'];
        $this->ENUM = array_column(Enumerator::cases(), null, 'name');
    }

    # Get an option
    public function getOption(string $name): mixed
    {
        return $this->uss->options->get($name);
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
}