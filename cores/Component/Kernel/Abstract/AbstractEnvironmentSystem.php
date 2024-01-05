<?php

namespace Uss\Component\Kernel\Abstract;

use Uss\Component\Kernel\UssImmutable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Uss\Component\Kernel\Enumerator;
use Uss\Component\Kernel\Interface\UssInterface;
use Uss\Component\Kernel\System\Extension;

abstract class AbstractEnvironmentSystem
{
    public readonly FilesystemLoader $filesystemLoader;
    public readonly Environment $twigEnvironment;
    protected Extension $extension;
    public array $twigContext;
    public array $jsCollection = [];
    protected array $properties = [];

    public function __construct()
    {
        $this->twigContext = [
            'html_language' => 'en',
            'page_title' => UssImmutable::PROJECT_NAME,
            'page_icon' => $this->pathToUrl(UssImmutable::ASSETS_DIR . '/images/origin.png'),
            'page_description' => "A Modular PHP Framework for Building Customized Web Applications",
        ];

        $this->filesystemLoader = new FilesystemLoader([UssImmutable::TEMPLATES_DIR]);
        $this->filesystemLoader->addPath(UssImmutable::TEMPLATES_DIR, UssInterface::NAMESPACE);
        $this->twigEnvironment = new Environment($this->filesystemLoader, [
            'debug' => UssImmutable::DEBUG,
        ]);
        $this->twigEnvironment->addExtension(new DebugExtension());

        $this->extension = new Extension($this);
        $this->twigEnvironment->addGlobal(UssInterface::NAMESPACE, $this->extension);
    }

    /**
    * Generate URL from an absolute filesystem path.
    *
    * @param string $pathname The pathname to be converted in the URL.
    * @param bool $hidebase Whether to hide the URL base or not.
    */
    public function pathToUrl(string $pathname, bool $hideProtocol = false): string
    {
        $pathname = $this->useForwardSlash($pathname); // Necessary in windows OS
        $port = $_SERVER['SERVER_PORT'];
        $scheme = ($_SERVER['REQUEST_SCHEME'] ?? ($port != 80 ? 'https' : 'http'));
        $viewPort = !in_array($port, ['80', '443']) ? ":{$port}" : null;
        $requestUri = preg_replace("~^{$_SERVER['DOCUMENT_ROOT']}~i", '', $pathname);

        return (!$hideProtocol || $viewPort) ?
            $scheme . "://" . $_SERVER['SERVER_NAME'] . "{$viewPort}" . $requestUri :
            $requestUri;
    }

    /**
     * Explode a content by a seperator and rejoin the filtered value
     */
    public function filterContext(string|array $path, string $divider = '/'): string
    {
        if(is_array($path)) {
            $path = implode($divider, $path);
        };
        $explosion = array_filter(array_map('trim', explode("/", $path)));
        return implode("/", $explosion);
    }

    /**
    * Convert a namespace path to file system path or URL
    */
    public function getTemplateSchema(?string $templatePath = UssInterface::NAMESPACE, Enumerator $enum = Enumerator::FILE_SYSTEM, int $index = 0): string
    {
        $templatePath = $this->filterContext($templatePath);
        if(!preg_match('/^@\w+/i', $templatePath)) {
            $templatePath = '@' . UssInterface::NAMESPACE . '/' . $templatePath;
        }

        $context = explode("/", $templatePath);
        $namespace = str_replace('@', '', array_shift($context));
        $filesystem = $this->filesystemLoader->getPaths($namespace)[$index] ?? null;
        $prefix = '';

        if($filesystem) {
            $prefix = match($enum) {
                Enumerator::FILE_SYSTEM => $filesystem,
                Enumerator::THEME => "@{$namespace}",
                default => $this->pathToUrl($filesystem)
            };
        }

        return $prefix . '/' . $this->filterContext(implode('/', $context));
    }

    /**
     * Replaces backslashes with forward slashes in a given string.
     */
    protected function useForwardSlash(?string $path): string
    {
        return str_replace("\\", "/", $path);
    }
}
