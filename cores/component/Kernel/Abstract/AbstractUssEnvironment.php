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
    public readonly Environment $twig;
    public array $templateContext;
    public array $jsCollection = [];

    protected const ENV_CONFIG = [
        'debug' => true,
    ];

    public function __construct()
    {
        $this->filesystemLoader = new FilesystemLoader([UssImmutable::TEMPLATES_DIR]);
        $this->filesystemLoader->addPath(UssImmutable::TEMPLATES_DIR, UssImmutable::APP_NAMESPACE);
        $this->twig = new Environment($this->filesystemLoader, self::ENV_CONFIG);
        $this->twig->addExtension(new DebugExtension());
        $this->templateContext = $this->createSystemContext();
    }

    private function createSystemContext(): array
    {
        return [
            'html_language' => 'en',
            'page_title' => UssImmutable::PROJECT_NAME,
            'page_icon' => $this->pathToUrl(UssImmutable::ASSETS_DIR . '/images/origin.png', false),
            'page_slogan' => 'A Modular PHP Framework for Building Customized Web Applications',
            'page_description' => "User Synthetics is a powerful and versatile PHP framework designed to simplify the development of customizable and extensible web applications.",
        ];
    }
}
