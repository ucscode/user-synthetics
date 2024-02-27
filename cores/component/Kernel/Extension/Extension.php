<?php

namespace Uss\Component\Kernel\Extension;

use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Uss\Component\Kernel\Resource\Enumerator;
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
    public readonly array $ENUM;
    public readonly array $immutable;
    public readonly string $jsCollectionEncoded;

    protected bool $configured = false;
    protected AccessibleProperties $accessibleProperties;
    protected AccessibleMethods $accessibleMethods;
    protected ExtensionAppObject $extensionAppObject;

    public function __construct(protected UssInterface $framework)
    {
        $this->ENUM = array_column(Enumerator::cases(), null, 'name');
        $this->immutable = (new ReflectionClass(UssImmutable::class))->getConstants();
        $this->initializeAccessibleProperties();
        $this->initializeAccessibleMethods();
        $this->extensionAppObject = new ExtensionAppObject($this->framework);
    }

    public function getGlobals(): array
    {
        return [
            UssImmutable::APP_EXTENSION_KEY => $this,
        ];
    }

    public function configureRenderContext(): void
    {
        if(!$this->configured) {
            $this->framework->jsCollection['platform'] = UssImmutable::PROJECT_NAME;
            $this->framework->jsCollection['url'] = $this->framework->pathToUrl(ROOT_DIR, false);
            $this->framework->templateContext['favicon'] ??= $this->framework->templateContext['page_logo'];
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

    public function app(): ExtensionAppObject
    {
        return $this->extensionAppObject;
    }

    protected function initializeAccessibleProperties(): void
    {
        $this->accessibleProperties = new AccessibleProperties([], $this->framework);
    }

    protected function initializeAccessibleMethods(): void
    {
        $this->accessibleMethods = new AccessibleMethods([
                'pathToUrl',
                'keygen',
                'getTemplateSchema',
                'relativeTime',
                'arrayToHtmlAttrs',
                'replaceUrlQuery'
            ],
            $this->framework
        );
    }
}
