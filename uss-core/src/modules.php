<?php

use Ucscode\Event\Event;

defined('ROOT_DIR') || die('@CORE:MODULE');

new class {

    private array $modules = [];
    private array $loaded = [];

    public function __construct() 
    {
        $this->iterateModules();
        $this->emitFinal();
    }

    private function iterateModules(): void
    {
        $iterator = new FileSystemIterator(UssEnum::MOD_DIR);
        foreach($iterator as $system) {
            if($system->isDir()) {
                $configFile = $system->getPathname() . "/config.json";
                if(is_file($configFile)) {
                    $this->processJSON($configFile, $system);
                }
            }
        }
        $this->loadModules();
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
                        'Unable to load JSON context of %s because the "name" attribute is missing',
                        $configFile
                    ),
                    E_USER_WARNING
            );
            } else {
                $path = $system->getPathname();
                $indexFile = $path . "/index.php";
                if(is_file($indexFile)) {
                    $this->modules[$path] = $config;
                }
            }
        } else {
            trigger_error(
                sprintf(
                    '%s: Unable to load JSON data of %s',
                    json_last_error_msg(),
                    $configFile
                ),
                E_USER_WARNING
            );
        }
    }

    private function loadModules(): void
    {
        foreach($this->modules as $path => $config) {
            $this->parseConfig($path, $config);
        }
    }

    private function parseConfig(string $path, array $config): void
    {
        if(empty($config['dependencies']) || !is_array($config['dependencies'])) {
            $config['dependencies'] = [];
        }
        $dependencies = $config['dependencies'];
        if(!empty($dependencies)) {
            $this->loadDependencies($dependencies);
        }
        $this->loadOnce($path);
    }

    private function loadOnce(string $path) {
        if(!in_array($path, $this->loaded)) {
            $this->loaded[] = $path;
            require_once $path . "/index.php";
        };
    }

    private function loadDependencies(array $dependencies) {
        foreach($dependencies as $name) {
            $dependency = $this->findModule($name);
            $this->parseConfig($dependency['path'], $dependency['config']);
        }
    }

    private function findModule(string $name): ?array {
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

    private function emitFinal(): void
    {
        // Load Modules
        Event::instance()->emit("Modules:loaded");

        // Render 404 Error
        $matchingRoutes = Route::getInventories(true);

        if(empty($matchingRoutes)) {
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                Uss::instance()->render('@Uss/error.html.twig');
            }
        }
    }

};


