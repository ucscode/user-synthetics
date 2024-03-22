<?php

namespace Ucscode\Uss;

use Uss\Component\Event\Event;
use Uss\Component\Kernel\UssImmutable;
use Uss\Component\Kernel\Uss;
use Uss\Component\Route\Route;

new class () 
{
    private string $jsonFile = 'config.json';
    private string $baseFile = 'index.php';

    private array $activeModules = [];
    private array $pendingModules = [];
    private array $loadedModules = [];
    private array $psr4 = [];

    public function __construct()
    {
        $this->iterateModules();
        $this->loadActiveModules();
        Event::emit("modules:loaded");
        $this->render404();
    }

    private function iterateModules(): void
    {
        $iterator = new \FileSystemIterator(UssImmutable::MODULES_DIR);
        foreach($iterator as $system) {
            if($system->isDir()) {
                $configFile = $system->getPathname() . "/" . $this->jsonFile;
                !is_file($configFile) ?: $this->processJSON($configFile, $system);
            }
        }
    }
    
    private function loadActiveModules(): void
    {
        $this->autoloadPSR4();
        foreach($this->activeModules as $path => $config) {
            $this->processModule($path, $config);
        }
    }

    private function processJSON(string $configFile, \SplFileInfo $system): void
    {
        $path = $system->getPathname();
        $baseFile = $path . "/" . $this->baseFile;
        $config = json_decode(file_get_contents($configFile), true);
        
        if(json_last_error()) {
            trigger_error(
                sprintf(
                    '%s: Unable to parse JSON data in %s',
                    json_last_error_msg(),
                    $configFile
                ),
                E_USER_WARNING
            );
            return;
        }
        
        if(is_file($baseFile) && !empty($config['enabled'] ?? true)) {

            array_walk_recursive($config, fn(&$value) => !is_string($value) ?: $value = trim($value));
            $config = $this->mergeComposerJson($path . "/composer.json", $config);
            
            if(empty($config['name'])) {
                trigger_error(
                    sprintf(
                        'Unable to load module located in "%s"; <b>%s</b> context requires "name" attribute',
                        pathinfo($configFile, PATHINFO_DIRNAME),
                        $this->jsonFile
                    ),
                    E_USER_WARNING
                );
                return;
            } 
            
            $config['autoload'] ??= [];
            $psr4 = is_array($config['autoload']) ? ($config['autoload']['psr-4'] ?? []) : null;
            
            if(!empty($psr4)) {

                $namespaces = array_map(function($value) {
                    substr($value, -1) === '\\' ?: $value .= '\\';
                    return $value;
                }, array_keys($psr4));

                $this->psr4 = array_merge(
                    $this->psr4, 
                    array_combine($namespaces, $psr4)
                );
            }
            
            $this->activeModules[$path] = $config;
        }
    }

    private function processModule(string $path, array $config): void
    {
        $dependencies = !is_array($config['dependencies'] ?? null) ? [] : $config['dependencies'];
        !empty($dependencies) ? $this->loadDependencies($path, $dependencies) : null;
        $this->loadOnce($path, $config);
    }

    private function loadDependencies(string $path, array $dependencies): void
    {
        if(!$this->isLoaded($path) && !$this->isPending($path)) {
            $this->pendingModules[] = $path;
            foreach($dependencies as $name) {
                $dependency = $this->findModule($name);
                if($dependency) {
                    $this->processModule($dependency['path'], $dependency['config']);
                    continue;
                } 
                trigger_error(
                    sprintf(
                        'Dependency Failure: No such module with the name "%s" as described in %s',
                        $name,
                        $path . '/' . $this->jsonFile
                    ),
                    E_USER_WARNING
                );
            }
        }
    }

    private function loadOnce(string $path, array $config): void
    {
        if(!$this->isLoaded($path)) {
            $this->loadedModules[] = $path;
            require_once $path . "/" . $this->baseFile;
        };
    }

    private function findModule(string $name): ?array
    {
        foreach($this->activeModules as $path => $config) {
            if($config['name'] === $name) {
                return [
                    'path' => $path,
                    'config' => $config
                ];
            }
        }
        return null;
    }

    private function isLoaded(string $path): bool
    {
        return in_array($path, $this->loadedModules);
    }

    private function isPending(string $path): bool
    {
        return in_array($path, $this->pendingModules);
    }

    private function render404(): void
    {
        $matchingRoutes = Route::getInventories(true);
        $isGetRequest = $_SERVER['REQUEST_METHOD'] === 'GET';
        if(empty($matchingRoutes) && $isGetRequest) {
            Uss::instance()->render('@Uss/error.html.twig');
        }
    }

    private function autoloadPSR4(): void
    {
        spl_autoload_register(function(string $fqcn): void {
            foreach($this->psr4 as $namespace => $directory) {
                $baseDirectory = UssImmutable::MODULES_DIR . DIRECTORY_SEPARATOR . trim($directory);
                substr($baseDirectory, -1) === '/' ?: $baseDirectory .= '/';
                $len = strlen($namespace);
                if(strncmp($namespace, $fqcn, $len) === 0) {
                    $relativeClass = substr($fqcn, $len);
                    $file = $baseDirectory . str_replace('\\', '/', $relativeClass) . '.php';
                    if(is_file($file)) {
                        require_once($file);
                        break;
                    }
                }
            };
        });
    }

    private function mergeComposerJson(string $composerFile, array $config): array
    {
        if(is_file($composerFile)) {
            $context = json_decode(file_get_contents($composerFile), true);
            $config = array_merge($context, $config);
        }
        return $config;
    }
};
