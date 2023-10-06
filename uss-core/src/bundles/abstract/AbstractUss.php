<?php

use Ucscode\Packages\Pairs;
use Twig\Loader\FilesystemLoader;

abstract class AbstractUss extends AbstractUssHelper implements UssInterface
{
    public static array $globals = [];

    public readonly ?Pairs $options;
    public readonly ?\mysqli $mysqli;
    protected readonly ?FilesystemLoader $twigLoader;
    protected string $namespace = 'Uss';

    protected array $twigExtensions = [];
    protected array $consoleJS = [];
    protected static array $routes = [];

    protected function __construct()
    {
        $this->twigLoader = new FilesystemLoader();
        $this->twigLoader->addPath(UssEnum::VIEW_DIR, $this->namespace);
        $this->twigLoader->addPath(UssEnum::VIEW_DIR, '__main__');

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

    /**
    * @ignore
    */
    protected function refactorNamespace(string $templatePath): string
    {
        if(substr($templatePath, 0, 1) === '@') {
            $split = explode("/", $templatePath);
            $split[0][1] = strtoupper($split[0][1]);
            $templatePath = implode("/", $split);
        };
        return $templatePath;
    }

    /**
    * Validate the provided Twig namespace.
    *
    * @param string $namespace The Twig namespace to validate.
    *
    * @throws \Exception If the namespace contains invalid characters or matches the current namespace.
    */
    private function validateNamespace(string $namespace): string
    {
        if (!preg_match("/^\w+$/i", $namespace)) {
            throw new \Exception(
                sprintf('%s: Twig namespace may only contain letters, numbers, and underscores.', __METHOD__)
            );
        }

        if (strtolower($namespace) === strtolower($this->namespace)) {
            throw new \Exception(
                sprintf('%s: Use of `%s` as a namespace is not allowed.', __METHOD__, $namespace)
            );
        }

        return ucfirst($namespace);
    }

    /**
    * @ignore
    */
    private function loadTwigAssets()
    {
        $vendors = [
            'head_css' => [
                'bootstrap' => 'css/bootstrap.min.css',
                'bs-icon' => 'vendor/bootstrap-icons/bootstrap-icons.min.css',
                'animate' => 'css/animate.min.css',
                'glightbox' => "vendor/glightbox/glightbox.min.css",
                'izitoast' => 'vendor/izitoast/css/iziToast.min.css',
                'font-size' => "css/font-size.min.css",
                'main-css' => 'css/main.css'
            ],
            'body_js' => [
                'jquery' => 'js/jquery-3.7.1.min.js',
                'bootstrap' => 'js/bootstrap.bundle.min.js',
                'bootbox' => 'js/bootbox.all.min.js',
                'glightbox' => "vendor/glightbox/glightbox.min.js",
                'izitoast' => 'vendor/izitoast/js/iziToast.min.js',
                'notiflix-loading' => 'vendor/notiflix/notiflix-loading-aio-3.2.6.min.js',
                'notiflix-block' => 'vendor/notiflix/notiflix-block-aio-3.2.6.min.js',
                'main-js' => 'js/main.js'
            ]
        ];

        $blockManager = UssTwigBlockManager::instance();

        foreach($vendors as $block => $contents) {

            $contents = array_map(function ($value) {

                $type = explode(".", $value);
                $value = $this->abspathToUrl(UssEnum::ASSETS_DIR . "/" . $value);

                if(strtolower(end($type)) === 'css') {
                    $element = "<link rel='stylesheet' href='" . $value . "'>";
                } else {
                    $element = "<script type='text/javascript' src='" . $value . "'></script>";
                };

                return $element;

            }, $contents);

            $blockManager->appendTo($block, $contents);

        };
    }

    private function loadUssDatabase(): void
    {
        if(DB_ENABLED) {
            try {

                // Initialize Mysqli
                $this->mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

                if($this->mysqli->connect_errno) {
                    throw new \Exception($this->mysqli->connect_error);
                } else {
                    try {
                        // Initialize Pairs
                        $this->options = new Pairs($this->mysqli, DB_PREFIX . "options");
                    } catch(\Exception $e) {
                        $this->render('@Uss/error.html.twig', [
                            'subject' => "Library Error",
                            'message' => $e->getMessage()
                        ]);
                        die();
                    }
                }

            } catch(\Exception $e) {

                $this->render('@Uss/db.error.html.twig', [
                    'error' => $e->getMessage(),
                    'url' => UssEnum::GITHUB_REPO,
                    'mail' => UssEnum::AUTHOR_EMAIL
                ]);

                die();

            };

        } else {
            $this->mysqli = $this->options = null;
        }
    }

    private function loadUssVariables()
    {
        self::$globals['icon'] = $this->abspathToUrl(UssEnum::ASSETS_DIR . '/images/origin.png');
        self::$globals['title'] = UssEnum::PROJECT_NAME;
        self::$globals['headline'] = "Modular PHP Framework for Customizable Platforms";
        self::$globals['description'] = "Empowering Web Developers with a Modular PHP Framework for Customizable and Extensible Web Platforms.";
    }

    private function loadUssSession()
    {
        if(empty(session_id())) {
            session_start();
        }

        $sidIndex = 'USSID';

        if(empty($_SESSION[$sidIndex])) {
            $_SESSION[$sidIndex] = $this->keygen(40, true);
        };

        $cookieIndex = 'USSCLIENTID';

        if(empty($_COOKIE[$cookieIndex])) {

            $time = (new \DateTime())->add((new \DateInterval("P3M")));

            $_COOKIE[$cookieIndex] = uniqid($this->keygen(7));

            $setCookie = setrawcookie($cookieIndex, $_COOKIE[$cookieIndex], $time->getTimestamp(), '/');

        };
    }

}
