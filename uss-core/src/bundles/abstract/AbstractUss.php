<?php

use Twig\Loader\FilesystemLoader;

abstract class AbstractUss extends AbstractUssUtils
{
    protected readonly ?FilesystemLoader $twigLoader;
    protected array $consoleJS = [];
    protected array $twigExtensions = [];

    protected function __construct()
    {
        $this->twigLoader = new FilesystemLoader();
        $this->twigLoader->addPath(UssImmutable::VIEW_DIR, $this->namespace);
        $this->twigLoader->addPath(UssImmutable::VIEW_DIR, '__main__');
        $this->loadTwigAssets();
        $this->loadUssDatabase();
        $this->loadUssSession();
        $this->loadUssVariables();
    }

    /**
    * Add a Twig filesystem path with a specified namespace.
    *
    * @param string $directory The directory path to add.
    * @param string $namespace The namespace for the Twig filesystem path.
    *
    * @throws \Exception If the namespace contains invalid characters, is already in use, or matches the current namespace.
    */
    public function addTwigFilesystem(string $directory, string $namespace): void
    {
        $namespace = $this->validateNamespace($namespace);

        if (in_array($namespace, $this->twigLoader->getNamespaces())) {
            throw new \Exception(
                sprintf('%s: `%s` namespace already exists.', __METHOD__, $namespace)
            );
        }

        $this->twigLoader->addPath($directory, $namespace);
    }


    /**
    * Adds a Twig extension to the environment.
    *
    * @param string $fullyQualifiedClassName The fully qualified class name of the Twig extension.
    *
    * @throws \Exception If the provided class does not implement Twig\Extension\ExtensionInterface.
    */
    public function addTwigExtension(string $fullyQualifiedClassName): void
    {
        $interfaceName = "Twig\\Extension\\ExtensionInterface";
        $fullyQualifiedClassName = trim($fullyQualifiedClassName);

        if (!in_array($interfaceName, class_implements($fullyQualifiedClassName))) {
            throw new \Exception(
                sprintf(
                    'The class "%s" provided to %s() must implement "%s".',
                    $fullyQualifiedClassName,
                    __METHOD__,
                    $interfaceName
                )
            );
        };

        if(!in_array($fullyQualifiedClassName, $this->twigExtensions)) {
            $this->twigExtensions[] = $fullyQualifiedClassName;
        };
    }


    /**
     * Pass a variable from PHP to JavaScript.
     *
     * @param string $key  The key or identifier for the data to be passed.
     * @param mixed $value The value to be associated with the given key.
     * @return void
     */
    public function addJsProperty(string $key, mixed $value): void
    {
        $this->consoleJS[$key] = $value;
    }

    /**
     * Get a registered JavaScript variable
     *
     * @param string $key The key or identifier of the value to retrieve
     * @return mixed
     */
    public function getJsProperty(?string $key = null): mixed
    {
        if(is_null($key)) {
            return $this->consoleJS;
        };
        return $this->consoleJS[$key] ?? null;
    }


    /**
     * Remove a value from the list of consoled data.
     *
     * @param string $key The key or identifier of the value to be removed
     * @return mixed value of the removed property
     */
    public function removeJsProperty(string $key): mixed
    {
        $value = null;
        if(isset($this->consoleJS[$key])) {
            $value = $this->consoleJS[$key];
            unset($this->consoleJS[$key]);
        }
        return $value;
    }

    /**
     * Exit the script and print a JSON response.
     * @return void
     */
    public function exit(bool|int|null $status, ?string $message = null, array $data = []): void
    {
        $output = json_encode([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ], JSON_PRETTY_PRINT);
        exit($output);
    }

    /**
    * Kill the script and print a JSON response.
    * @return void
    */
    public function die(bool|int|null $status, ?string $message = null, array $data = []): void
    {
        $this->exit($status, $message, $data);
    }

    /**
     * Explode a content by a seperator and rejoin the filtered value
     */
    public function filterContext(string|array $path, string $divider = '/'): string
    {
        if(is_array($path)) {
            $path = implode($divider, $path);
        };
        return implode($divider, array_filter(
            array_map('trim', explode($divider, $path)),
            function ($value) {
                return trim($value) !== '';
            }
        ));
    }

}
