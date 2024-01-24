<?php

namespace Uss\Component\Kernel\Abstract;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Uss\Component\Kernel\Interface\UssFrameworkInterface;
use Uss\Component\Kernel\UssImmutable;

abstract class AbstractUssEnvironment implements UssFrameworkInterface
{
    public readonly FilesystemLoader $filesystemLoader;
    public readonly Environment $twigEnvironment;
    public array $twigContext;
    public array $jsCollection = [];
    
    protected const ENV_CONFIG = [
        'debug' => UssImmutable::DEBUG,
    ];

    public function __construct()
    {
        $this->filesystemLoader = new FilesystemLoader([UssImmutable::TEMPLATES_DIR]);
        $this->filesystemLoader->addPath(UssImmutable::TEMPLATES_DIR, UssImmutable::NAMESPACE);
        $this->twigEnvironment = new Environment($this->filesystemLoader, self::ENV_CONFIG);
        $this->twigEnvironment->addExtension(new DebugExtension());
        $this->twigContext = $this->createSystemContext();
    }

    private function createSystemContext(): array
    {
        return [
            'html_language' => 'en',
            'page_title' => UssImmutable::PROJECT_NAME,
            'page_icon' => $this->pathToUrl(UssImmutable::ASSETS_DIR . '/images/origin.png', false),
            'page_description' => "A Modular PHP Framework for Building Customized Web Applications",
        ];
    }
}
