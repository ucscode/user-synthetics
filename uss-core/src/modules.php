<?php

use Ucscode\Event\Event;

defined('ROOT_DIR') || die('@CORE:MODULE');

new class {

    private string $jsonFile = 'config.json';
    private string $baseFile = 'index.php';
    private array $modules = [];
    private array $pending = [];
    private array $loaded = [];

    public function __construct() 
    {
        $this->iterateModules();
        $this->loadActiveModules();
        Event::instance()->emit("modules:loaded");
        $this->render404();
    }

    private function iterateModules(): void
    {
        $iterator = new FileSystemIterator(UssEnum::MOD_DIR);
        
        foreach($iterator as $system) {
            if($system->isDir()) {
                $configFile = $system->getPathname() . "/" . $this->jsonFile;
                if(is_file($configFile)) {
                    $this->processJSON($configFile, $system);
                }
            }
        }
    }

    private function processJSON(string $configFile, SplFileInfo $system): void
    {
        $config = json_decode(file_get_contents($configFile), true);

        if(!json_last_error()) {
            array_walk_recursive($config, function(&$value) {
                $value = trim($value);
            });
            if(empty($config['name'])) {
                trigger_error(
                    sprintf(
                        'Unable to load module located in "%s"; <b>%s</b> context requires "name" attribute',
                        pathinfo($configFile, PATHINFO_DIRNAME),
                        $this->jsonFile
                    ),
                    E_USER_WARNING
            );
            } else {
                $path = $system->getPathname();
                $baseFile = $path . "/" . $this->baseFile;
                if(is_file($baseFile)) {
                    $this->modules[$path] = $config;
                }
            }
        } else {
            trigger_error(
                sprintf(
                    '%s: Unable to parse JSON data in %s',
                    json_last_error_msg(),
                    $configFile
                ),
                E_USER_WARNING
            );
        }
    }

    private function loadActiveModules(): void
    {
        foreach($this->modules as $path => $config) {
            $this->handleConfig($path, $config);
        }
    }

    private function handleConfig(string $path, array $config): void
    {
        if(!is_array($config['dependencies'] ?? null)) {
            $config['dependencies'] = [];
        }

        $dependencies = $config['dependencies'];

        if(!empty($dependencies)) {
            $this->loadDependencies($path, $dependencies);
        }

        $this->loadOnce($path);
    }

    private function loadDependencies(string $path, array $dependencies): void
    {
        if(!$this->isLoaded($path) && !$this->isPending($path)) {
            $this->pending[] = $path;
            foreach($dependencies as $name) {
                $dependency = $this->findModule($name);
                if($dependency) {
                    $this->handleConfig($dependency['path'], $dependency['config']);
                } else {
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
    }

    private function loadOnce(string $path): void
    {
        if(!$this->isLoaded($path)) {
            $this->loaded[] = $path;
            require_once $path . "/" . $this->baseFile;
        };
    }

    private function findModule(string $name): ?array 
    {
        foreach($this->modules as $path => $config) {
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
        return in_array($path, $this->loaded);
    }

    private function isPending(string $path): bool 
    {
        return in_array($path, $this->pending);
    }

    private function render404(): void
    {
        $matchingRoutes = Route::getInventories(true);
        $isGetRequest = $_SERVER['REQUEST_METHOD'] === 'GET';

        if(empty($matchingRoutes) && $isGetRequest) {
            Uss::instance()->render('@Uss/error.html.twig');
        }
    }

};


