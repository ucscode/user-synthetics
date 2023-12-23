<?php

namespace Ucscode\Uss;

use Uss\Component\Event\Event;
use Uss\Component\Kernel\UssImmutable;
use Uss\Component\Kernel\Uss;
use Uss\Component\Route\Route;

/**
 * Module Loader
 *
 * This file defines an anonymous class responsible for loading modules in the project.
 * The class dynamically loads and initializes modules based on the project's requirements.
 *
 * @package Ucscode\Uss
 */
new class () {
    /**
     * The name of the configuration file required for each modules
     * @var string
     */
    private string $jsonFile = 'config.json';

    /**
     * The base file required for each modules
     * @var string
     */
    private string $baseFile = 'index.php';

    /**
     * The list of active available modules in the project
     * @var array
     */
    private array $modules = [];

    /**
     * Modules awaiting the loading of a dependency module.
     * @var array
     */
    private array $pending = [];

    /**
     * Loaded modules to avoid unconditional reloading.
     * @var array
     */
    private array $loaded = [];

    public function __construct()
    {
        $this->iterateModules();
        $this->loadActiveModules();
        Event::emit("modules:loaded");
        $this->render404();
    }

    /**
     * Module Directory Iterator
     *
     * This iterates the modules directory and discover all modules that has configuration file
     */
    private function iterateModules(): void
    {
        $iterator = new \FileSystemIterator(UssImmutable::MOD_DIR);

        foreach($iterator as $system) {
            if($system->isDir()) {
                $configFile = $system->getPathname() . "/" . $this->jsonFile;
                if(is_file($configFile)) {
                    $this->processJSON($configFile, $system);
                }
            }
        }
    }

    /**
     * Process Modules Configuration File
     *
     * This inspects the modules configuration file and add the modules into the
     * available module list if not error is encountered
     */
    private function processJSON(string $configFile, \SplFileInfo $system): void
    {
        $config = json_decode(file_get_contents($configFile), true);

        if(!json_last_error()) {
            array_walk_recursive($config, function (&$value) {
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

    /**
     * Load Active Modules
     *
     * This iterates throught all active modules and load them individually
     */
    private function loadActiveModules(): void
    {
        foreach($this->modules as $path => $config) {
            $this->handleConfig($path, $config);
        }
    }

    /**
     * Check for dependencies
     *
     * This checks if the module is dependent on another modules before loading.
     * If the modules has dependency, it loads all dependencies first before loading the module
     */
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

    /**
     * Dependency Loader
     *
     * This method is the logic behind dependency loading.
     * It checks if a module is loaded or is pending to avoid loading dependencies multiple times
     */
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

    /**
     * Load Module Once
     *
     * This checks if a module has already been loaded and ignores further loading of the module
     */
    private function loadOnce(string $path): void
    {
        if(!$this->isLoaded($path)) {
            $this->loaded[] = $path;
            require_once $path . "/" . $this->baseFile;
        };
    }

    /**
     * Find a module
     *
     * Each module must have a unique name.
     * This method finds the module and returns the module's path and configuration information
     */
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

    /**
     * This checks if a modules is already loaded
     */
    private function isLoaded(string $path): bool
    {
        return in_array($path, $this->loaded);
    }

    /**
     * This checks if a modules is in pending state
     */
    private function isPending(string $path): bool
    {
        return in_array($path, $this->pending);
    }

    /**
     * Render 404 Error Pages
     *
     * When there is no router to handle a route or display anything on the screen
     * This method will automatically render a 404 error page
     */
    private function render404(): void
    {
        $matchingRoutes = Route::getInventories(true);
        $isGetRequest = $_SERVER['REQUEST_METHOD'] === 'GET';
        if(empty($matchingRoutes) && $isGetRequest) {
            Uss::instance()->render('@Uss/error.html.twig');
        }
    }

};
