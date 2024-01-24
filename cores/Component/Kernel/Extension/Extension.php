<?php

namespace Uss\Component\Kernel\Extension;

use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Uss\Component\Kernel\Resource\Enumerator;
use Uss\Component\Block\BlockManager;
use Uss\Component\Kernel\Resource\AccessibleMethods;
use Uss\Component\Kernel\Interface\UssInterface;
use Uss\Component\Kernel\Resource\AccessibleProperties;
use Uss\Component\Kernel\UssImmutable;

/**
 * This extension is a minified version of Uss class for twig
 * It provides only limited properties and methods from the Uss class to the twig template
 */
final class Extension extends AbstractExtension implements ExtensionInterface, GlobalsInterface
{
    public readonly string $jsCollectionEncoded;
    public readonly array $ENUM;
    public readonly array $immutable;
    protected bool $configured = false;
    protected AccessibleProperties $accessibleProperties;
    protected AccessibleMethods $accessibleMethods;

    public function __construct(protected UssInterface $framework)
    {
        $this->ENUM = array_column(Enumerator::cases(), null, 'name');
        $this->immutable = (new ReflectionClass(UssImmutable::class))->getConstants();
        $this->initializeAccessibleProperties();
        $this->initializeAccessibleMethods();
    }

    public function getGlobals(): array
    {
        return [
            UssImmutable::EXTENSION_KEY => $this,
        ];
    }

    public function configureRenderContext(): void
    {
        if(!$this->configured) {
            $this->framework->jsCollection['platform'] = UssImmutable::PROJECT_NAME;
            $this->framework->jsCollection['url'] = $this->framework->pathToUrl(ROOT_DIR, false);
            $this->framework->twigContext['favicon'] ??= $this->framework->twigContext['page_icon'];
            $this->jsCollectionEncoded = base64_encode(json_encode($this->framework->jsCollection));
            $this->configured = true;
        }
    }

    public function props(): AccessibleProperties
    {
        return $this->accessibleProperties;
    }

    public function meths(): AccessibleMethods
    {
        return $this->accessibleMethods;
    }

    # Get an option
    public function getOption(string $name): mixed
    {
        return $this->framework->options->get($name);
    }

    /**
     * Self Methods
     */
    public function renderBlockElements(string $blockName, array $_context): ?string
    {
        $outputs = [];

        if($block = BlockManager::instance()->getBlock($blockName)) {
            
            $templates = $block->getTemplates();
            uasort($templates, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());

            foreach($templates as $name => $blockTemplate) {
                if(!$blockTemplate->isRendered()) {
                    $blockTemplate->fulfilled();
                    $outputs[] = $this->framework->twigEnvironment
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

    protected function initializeAccessibleProperties(): void
    {
        $this->accessibleProperties = new AccessibleProperties($this->framework, []);
    }

    protected function initializeAccessibleMethods(): void
    {
        $this->accessibleMethods = new AccessibleMethods($this->framework, [
            'pathToUrl',
            'keygen',
            'getTemplateSchema',
            'relativeTime',
            'arrayToHtmlAttrs'
        ]);
    }
}
