<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

abstract class AbstractUss extends AbstractUssUtils
{
    public readonly FilesystemLoader $filesystemLoader;
    public readonly Environment $twigEnvironment;
    protected array $consoleJS = [];

    protected function __construct()
    {
        $this->filesystemLoader = new FilesystemLoader([UssImmutable::VIEW_DIR]);
        $this->filesystemLoader->addPath(UssImmutable::VIEW_DIR, self::NAMESPACE);
        
        $this->twigEnvironment = new Environment($this->filesystemLoader, [
            'debug' => UssImmutable::DEBUG
        ]);

        $this->twigEnvironment->addExtension(new DebugExtension());
        $this->twigEnvironment->addGlobal(self::NAMESPACE, new UssTwigExtension($this));

        $this->twigComponentLoader();
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
