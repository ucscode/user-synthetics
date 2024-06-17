<?php

namespace Uss\Component\Kernel\Abstract;

use Twig\TemplateWrapper;
use Uss\Component\Kernel\Abstract\AbstractUss;
use Uss\Component\Kernel\Extension\Extension;
use Uss\Component\Kernel\Uss;

abstract class AbstractSandbox extends AbstractUss
{
    protected Extension $borrowedExtension;

    public function __construct()
    {
        parent::__construct();
        $this->inheritUssFilesystemPaths();
        $this->borrowedExtension = new Extension(Uss::instance());
        $this->twig->addExtension($this->borrowedExtension);
    }
    
    public function render(string|TemplateWrapper $template, array $context = []): string
    {
        $this->borrowedExtension->configureRenderContext();
        $context += Uss::instance()->templateContext;
        return $this->twig->render($template, $context);
    }

    private function inheritUssFilesystemPaths(): void
    {
        $ussLoader = Uss::instance()->filesystemLoader;
        $danglingNamespaces = array_diff($ussLoader->getNamespaces(), $this->filesystemLoader->getNamespaces());
        foreach($danglingNamespaces as $namespace) {
            $this->filesystemLoader->setPaths($ussLoader->getPaths($namespace), $namespace);
        }
    }
}