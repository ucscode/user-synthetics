<?php

namespace Uss\Component\Kernel\Abstract;

use Uss\Component\Kernel\UssImmutable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Uss\Component\Kernel\Interface\UssFrameworkInterface;
use Uss\Component\Kernel\Interface\UssInterface;
use Uss\Component\Kernel\System\Extension;

abstract class AbstractUssEnvironment implements UssFrameworkInterface
{
    public readonly FilesystemLoader $filesystemLoader;
    public readonly Environment $twigEnvironment;
    public array $twigContext;
    public array $jsCollection = [];
    protected array $properties = [];
    protected Extension $extension;

    public function __construct()
    {
        $this->filesystemLoader = new FilesystemLoader([UssImmutable::TEMPLATES_DIR]);

        $this->filesystemLoader->addPath(UssImmutable::TEMPLATES_DIR, UssInterface::NAMESPACE);
        $this->twigEnvironment = new Environment($this->filesystemLoader, [
            'debug' => UssImmutable::DEBUG,
        ]);
        $this->twigEnvironment->addExtension(new DebugExtension());

        $this->twigContext = [
            'html_language' => 'en',
            'page_title' => UssImmutable::PROJECT_NAME,
            'page_icon' => $this->pathToUrl(UssImmutable::ASSETS_DIR . '/images/origin.png', false),
            'page_description' => "A Modular PHP Framework for Building Customized Web Applications",
        ];

        $this->extension = new Extension($this);
        $this->twigEnvironment->addGlobal(UssInterface::NAMESPACE, $this->extension);
    }
}
