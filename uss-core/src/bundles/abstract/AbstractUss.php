<?php

use Ucscode\Packages\Pairs;
use Twig\Loader\FilesystemLoader;

abstract class AbstractUss extends AbstractUssHelper implements UssInterface
{
    use PropertyAccessTrait;
    
    public static array $globals = [];

    public readonly ?Pairs $options;
    public readonly ?\mysqli $mysqli;

    protected readonly ?FilesystemLoader $twigLoader;
    protected string $namespace = 'Uss';
    protected bool $rendered = false;

    protected array $console = [];
    protected array $routes = [];

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

    public function getContext(string $name): mixed {
        //return 
    }

    /**
     * Register a template directory with in Uss
     */
    public function addTwigFilesystem(string $directory, string $namespace): void
    {
        if(!preg_match("/\w+/i", $namespace)) {
            throw new Exception(__METHOD__ . " #(Argument 1); Twig namespace may only contain letter, numbers and underscore");
        } elseif(strtolower($namespace) === strtolower($this->namespace)) {
            throw new Exception(__METHOD__ . " #(Argument 1); Use of `{$namespace}` as namespace is not allowed");
        };

        $namespace = ucfirst($namespace);

        if(in_array($namespace, $this->twigLoader->getNamespaces())) {
            throw new Exception(__METHOD__ . " #(Argument 1); `{$namespace}` namespace already exists");
        };

        $this->twigLoader->addPath($directory, $namespace);
    }

    /**
     * Get the current focus expression or list of focus expressions.
    */
    public function getRouteInventory(bool $authentic = false)
    {
        $routes = $this->routes;
        if($authentic) {
            $routes = array_filter($routes, function ($route) {
                return $route->authentic;
            });
        };
        return $routes;
    }

    /**
     * Exit the script and print a JSON response.
     * @return void
     */
    public function exit(?string $message = null, ?bool $status = null, ?array $data = [])
    {
        $args = func_get_args();
        if(empty($args)) {
            $output = '';
        } elseif(count($args) === 1) {
            $output = $message;
        } else {
            $output = json_encode([
                "message" => $message,
                "status" => (bool)$status,
                "data" => $data
            ]);
        };
        exit($output);
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
            array_map('trim', explode($divider, $path))
        ));
    }

    /**
     * @ignore
     */
    protected function localTwigExtension(UssTwigBlockManager $blockManager)
    {
        return new class ($this, $blockManager) {

            public string $jsElement;

            public function __construct(
                private Uss $uss,
                private UssTwigBlockManager $blockManager
            ) {
            }

            public function init(): self
            {
                $this->uss->console('platform', UssEnum::PROJECT_NAME);
                $jsonElement = json_encode($this->uss->console());
                $this->jsElement = base64_encode($jsonElement);
                return $this;
            }

            # Equivalent to call_user_func
            public function call(): mixed
            {
                $args = func_get_args();
                $callback = array_shift($args);
                $result = call_user_func_array([$this->uss, $callback], $args);
                return $result;
            }

            public function renderBlocks(string $name, int $indent = 1) {
                $blocks = $this->blockManager->getBlocks($name);
                if(is_array($blocks)) {
                    $indent = str_repeat("\t", abs($indent));
                    return implode("\n{$indent}", $blocks);
                };
            }

            # Get an option
            public function getOption(string $name)
            {
                return $this->uss->options->get($name);
            }

        };
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
    * @ignore
    */
    private function loadTwigAssets()
    {
        $vendors = [
            'head_css' => [
                'bootstrap' => 'css/bootstrap.min.css',
                'bs-icon' => 'vendor/bootstrap-icons/bootstrap-icons.css',
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

        $blockManager = new UssTwigBlockManager();
        
        foreach($vendors as $block => $contents) {
            $contents = array_map(function ($value) {
                $type = explode(".", $value);
                $value = $this->generateUrl(UssEnum::ASSETS_DIR . "/" . $value);
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

            };

        } else {
            $this->mysqli = $this->options = null;
        }
    }

    public function loadUssVariables()
    {
        self::$globals['icon'] = $this->generateUrl(UssEnum::ASSETS_DIR . '/images/origin.png');
        self::$globals['title'] = UssEnum::PROJECT_NAME;
        self::$globals['headline'] = "Modular PHP Framework for Customizable Platforms";
        self::$globals['description'] = "Empowering Web Developers with a Modular PHP Framework for Customizable and Extensible Web Platforms.";
    }

    public function loadUssSession()
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