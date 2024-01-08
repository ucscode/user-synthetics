<?php

namespace Uss\Component\Kernel\Abstract;

use Exception;
use Uss\Component\Block\BlockManager;
use Uss\Component\Block\BlockTemplate;
use Uss\Component\Kernel\Resource\Enumerator;
use Uss\Component\Kernel\Interface\UssFrameworkInterface;
use Uss\Component\Kernel\UssImmutable;

abstract class AbstractExtension
{
    public readonly string $jsCollectionEncoded;
    public readonly array $ENUM;
    protected bool $configured = false;

    public function __construct(protected UssFrameworkInterface $system)
    {
        // Do nothing until ready
    }

    public function configureRenderContext(): void
    {
        if(!$this->configured) {
            $this->system->jsCollection['platform'] = UssImmutable::PROJECT_NAME;
            $this->system->jsCollection['url'] = $this->system->pathToUrl(ROOT_DIR, false);
            $this->jsCollectionEncoded = base64_encode(json_encode($this->system->jsCollection));
            $this->system->twigContext['favicon'] ??= $this->system->twigContext['page_icon'];
            $this->ENUM = array_column(Enumerator::cases(), null, 'name');
            $this->configured = true;
        }
    }

    # Get an option
    public function getOption(string $name): mixed
    {
        return $this->system->options->get($name);
    }

    /**
     * Self Methods
     */
    public function renderBlockElements(string $name): ?string
    {
        $outputs = [];

        if($block = BlockManager::instance()->getBlock($name)) {
            // Render Templates First
            $templates = $block->getTemplates();
            usort($templates, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());

            array_walk($templates, function (BlockTemplate $blockTemplate) use (&$outputs) {
                $outputs[] = $this->system->twigEnvironment
                    ->resolveTemplate($blockTemplate->getTemplate())
                    ->render($blockTemplate->getContext());
            });

            // Render Contents Next;
            $contents = $block->getContents();
            usort($contents, fn ($a, $b) => $a['priority'] <=> $b['priority']);

            array_walk($contents, function (array $content) use (&$outputs) {
                $outputs[] = $content['content'];
            });
        }

        return implode("\n", $outputs);
    }
}