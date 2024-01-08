<?php

namespace Uss\Component\Kernel\Extension;

use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Uss\Component\Kernel\Resource\Enumerator;
use Uss\Component\Block\BlockManager;
use Uss\Component\Block\BlockTemplate;
use Uss\Component\Kernel\Resource\AccessibleMethods;
use Uss\Component\Kernel\Interface\UssFrameworkInterface;
use Uss\Component\Kernel\Resource\AccessibleProperties;
use Uss\Component\Kernel\UssImmutable;

/**
 * This extension is a minified version of Uss class for twig
 * It provides only limited properties and methods from the Uss class to the twig template
 */
final class Extension extends AbstractExtension implements ExtensionInterface
{
    public readonly string $jsCollectionEncoded;
    public readonly array $ENUM;
    public readonly array $immutable;
    protected bool $configured = false;
    protected AccessibleProperties $accessibleProperties;
    protected AccessibleMethods $accessibleMethods;

    public function __construct(protected UssFrameworkInterface $uss)
    {
        $this->ENUM = array_column(Enumerator::cases(), null, 'name');
        $this->immutable = (new ReflectionClass(UssImmutable::class))->getConstants();
        $this->initializeAccessibleProperties();
        $this->initializeAccessibleMethods();
    }

    public function configureRenderContext(): void
    {
        if(!$this->configured) 
        {
            $this->uss->jsCollection['platform'] = UssImmutable::PROJECT_NAME;
            $this->uss->jsCollection['url'] = $this->uss->pathToUrl(ROOT_DIR, false);
            $this->uss->twigContext['favicon'] ??= $this->uss->twigContext['page_icon'];
            
            $this->jsCollectionEncoded = base64_encode(
                json_encode($this->uss->jsCollection)
            );

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
        return $this->uss->options->get($name);
    }

    /**
     * Self Methods
     */
    public function renderBlockElements(string $name, array &$_context): ?string
    {
        $outputs = [];
        
        if($block = BlockManager::instance()->getBlock($name)) {
            // Render Templates First
            $templates = $block->getTemplates();
            usort($templates, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());

            array_walk($templates, function (BlockTemplate $blockTemplate) use (&$outputs, &$_context) {
                $outputs[] = $this->uss->twigEnvironment
                    ->resolveTemplate($blockTemplate->getTemplate())
                    ->render($blockTemplate->getContext() + $_context);
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

    protected function initializeAccessibleProperties(): void
    {
        $this->accessibleProperties = new AccessibleProperties($this->uss, [

        ]);
    }

    protected function initializeAccessibleMethods(): void
    {
        $this->accessibleMethods = new AccessibleMethods($this->uss, [
            'pathToUrl',
            'keygen',
            'getTemplateSchema',
            'relativeTime'
        ]);
    }
}
